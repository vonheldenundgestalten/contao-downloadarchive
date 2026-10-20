<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

namespace FelixPfeiffer\DownloadarchiveBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Filesystem\VirtualFilesystemInterface;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Symfony\Component\Uid\Uuid;

/**
 * "Read a directory": on saving an archive that has no files yet, adds one item
 * per file of the selected folder (optionally including subfolders and limited
 * to the given extensions).
 */
#[AsCallback(table: 'tl_downloadarchive', target: 'config.onsubmit')]
class ImportDirectoryListener
{
    public function __construct(
        private readonly VirtualFilesystemInterface $filesStorage,
        private readonly Connection $connection,
    ) {
    }

    public function __invoke(DataContainer $dc): void
    {
        $archive = $this->connection->fetchAssociative('SELECT * FROM tl_downloadarchive WHERE id = ?', [(int) $dc->id]);

        if (!$archive || !$archive['loadDirectory'] || !$archive['dirSRC']) {
            return;
        }

        // Only fill empty archives, so saving again does not import the files twice
        if ($this->connection->fetchOne('SELECT COUNT(*) FROM tl_downloadarchiveitems WHERE pid = ?', [$archive['id']]) > 0) {
            return;
        }

        try {
            $folder = Uuid::fromBinary($archive['dirSRC']);
        } catch (\Throwable) {
            return;
        }

        if (!$this->filesStorage->directoryExists($folder)) {
            return;
        }

        $extensions = array_map(strtolower(...), StringUtil::trimsplit(',', (string) $archive['extension']));
        $files = $this->filesStorage->listContents($folder, (bool) $archive['loadSubdir'])->files()->sort();
        $time = time();
        $count = 0;

        foreach ($files as $file) {
            if ([] !== $extensions && !\in_array($file->getExtension(true), $extensions, true)) {
                continue;
            }

            if (!$uuid = $file->getUuid()) {
                continue;
            }

            ++$count;

            $this->connection->insert(
                'tl_downloadarchiveitems',
                [
                    'pid' => $archive['id'],
                    'sorting' => $count * 128,
                    'tstamp' => $time,
                    'title' => $archive['prefix'] ? \sprintf('%s %04d', $archive['prefix'], $count) : StringUtil::specialchars($file->getName()),
                    'singleSRC' => $uuid->toBinary(),
                    'published' => (int) $archive['publishAll'],
                ],
                ['singleSRC' => ParameterType::BINARY],
            );
        }
    }
}
