<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

namespace FelixPfeiffer\DownloadarchiveBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

/**
 * Replaces the "show to guests only" checkbox of the files by the "guests"
 * member group (-1), like core did for content elements in Contao 5.
 */
class GuestsMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        return $schemaManager->tablesExist(['tl_downloadarchiveitems'])
            && isset($schemaManager->listTableColumns('tl_downloadarchiveitems')['guests']);
    }

    public function run(): MigrationResult
    {
        $rows = $this->connection->fetchAllAssociative("SELECT id, `groups` FROM tl_downloadarchiveitems WHERE guests = '1'");

        foreach ($rows as $row) {
            $groups = StringUtil::deserialize($row['groups'], true);
            $groups[] = -1;

            $this->connection->update(
                'tl_downloadarchiveitems',
                ['protected' => 1, '`groups`' => serialize(array_values(array_unique(array_map(intval(...), $groups))))],
                ['id' => $row['id']],
            );
        }

        $this->connection->executeStatement('ALTER TABLE tl_downloadarchiveitems DROP COLUMN guests');

        return $this->createResult(true);
    }
}
