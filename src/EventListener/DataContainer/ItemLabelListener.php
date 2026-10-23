<?php

declare(strict_types=1);

/*
 * This file is part of the vonheldenundgestalten/contao-downloadarchive package.
 *
 * (c) von Helden und Gestalten
 * (c) Felix Pfeiffer (original author)
 *
 * @license LGPL-3.0-or-later
 */

namespace VHUG\DownloadarchiveBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Filesystem\VirtualFilesystemInterface;
use Contao\StringUtil;
use Symfony\Component\Uid\Uuid;

/**
 * Back end list of the files of an archive: title, file path plus the start
 * of the description, and the published state.
 */
#[AsCallback(table: 'tl_downloadarchiveitems', target: 'list.label.label')]
class ItemLabelListener
{
    public function __construct(private readonly VirtualFilesystemInterface $filesStorage)
    {
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    public function __invoke(array $row): array
    {
        $preview = [];

        if ($path = $this->getPath($row['singleSRC'] ?? null)) {
            $preview[] = '<p><code>'.StringUtil::specialchars($path).'</code></p>';
        }

        if ($description = trim(strip_tags((string) ($row['description'] ?? '')))) {
            $preview[] = '<p>'.StringUtil::specialchars(StringUtil::substr(html_entity_decode($description), 200)).'</p>';
        }

        return [
            StringUtil::specialchars((string) $row['title']),
            implode('', $preview),
            $this->isPublished($row) ? 'published' : 'unpublished',
        ];
    }

    private function getPath(string|null $uuid): string|null
    {
        if (!$uuid) {
            return null;
        }

        try {
            return $this->filesStorage->get(Uuid::fromBinary($uuid))?->getPath();
        } catch (\Throwable) {
            return null;
        }
    }

    private function isPublished(array $row): bool
    {
        $time = time();

        return $row['published']
            && ('' === $row['start'] || $row['start'] <= $time)
            && ('' === $row['stop'] || $row['stop'] > $time);
    }
}
