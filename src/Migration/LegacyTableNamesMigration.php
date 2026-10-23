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

namespace VHUG\DownloadarchiveBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * Very old versions used "downloadarchiv" (without e) for the tables and the
 * archive selection of content elements and modules (formerly runonce.php).
 */
class LegacyTableNamesMigration extends AbstractMigration
{
    private const TABLES = [
        'tl_downloadarchiv' => 'tl_downloadarchive',
        'tl_downloadarchivitems' => 'tl_downloadarchiveitems',
    ];

    private const FIELD_TABLES = ['tl_content', 'tl_module'];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        return [] !== $this->getTablesToRename() || [] !== $this->getTablesWithOldField();
    }

    public function run(): MigrationResult
    {
        foreach ($this->getTablesToRename() as $old => $new) {
            $this->connection->executeStatement("RENAME TABLE $old TO $new");
        }

        foreach ($this->getTablesWithOldField() as $table) {
            $this->connection->executeStatement("ALTER TABLE $table CHANGE downloadarchiv downloadarchive text NULL");
        }

        return $this->createResult(true);
    }

    /**
     * @return array<string, string>
     */
    private function getTablesToRename(): array
    {
        $schemaManager = $this->connection->createSchemaManager();

        return array_filter(
            self::TABLES,
            static fn (string $new, string $old): bool => $schemaManager->tablesExist([$old]) && !$schemaManager->tablesExist([$new]),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    /**
     * @return list<string>
     */
    private function getTablesWithOldField(): array
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = [];

        foreach (self::FIELD_TABLES as $table) {
            if (!$schemaManager->tablesExist([$table])) {
                continue;
            }

            $columns = $schemaManager->listTableColumns($table);

            if (isset($columns['downloadarchiv']) && !isset($columns['downloadarchive'])) {
                $tables[] = $table;
            }
        }

        return $tables;
    }
}
