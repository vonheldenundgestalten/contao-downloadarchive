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
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

/**
 * Moves the archive permissions (tl_user(_group).downloadarchivep: create,
 * delete) into the generic table permissions of Contao 5.7 (cud). Editing
 * archives and managing their files was not restricted before, so these
 * permissions are granted.
 */
class ArchivePermissionsMigration extends AbstractMigration
{
    private const LEGACY_FIELD = 'downloadarchivep';

    private const MAPPING = [
        'create' => 'tl_downloadarchive::create',
        'delete' => 'tl_downloadarchive::delete',
    ];

    private const ALWAYS_GRANTED = [
        'tl_downloadarchive::update',
        'tl_downloadarchiveitems::create',
        'tl_downloadarchiveitems::update',
        'tl_downloadarchiveitems::delete',
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        return [] !== $this->getTables();
    }

    public function run(): MigrationResult
    {
        foreach ($this->getTables() as $table) {
            $where = 'tl_user' === $table ? "WHERE inherit IN ('extend', 'custom') AND admin = 0" : '';
            $rows = $this->connection->fetchAllAssociative('SELECT id, cud, '.self::LEGACY_FIELD." FROM $table $where");

            foreach ($rows as $row) {
                $legacy = StringUtil::deserialize($row[self::LEGACY_FIELD], true);
                $cud = array_diff(StringUtil::deserialize($row['cud'], true), self::MAPPING);

                foreach (self::MAPPING as $permission => $operation) {
                    if (\in_array($permission, $legacy, true)) {
                        $cud[] = $operation;
                    }
                }

                $cud = array_values(array_unique([...$cud, ...self::ALWAYS_GRANTED]));

                $this->connection->update($table, ['cud' => serialize($cud)], ['id' => $row['id']]);
            }

            $this->connection->executeStatement("ALTER TABLE $table DROP COLUMN ".self::LEGACY_FIELD);
        }

        return $this->createResult(true);
    }

    /**
     * Tables that still have the legacy field and already have the cud field,
     * which core adds in its own migration.
     *
     * @return list<string>
     */
    private function getTables(): array
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = [];

        foreach (['tl_user', 'tl_user_group'] as $table) {
            if (!$schemaManager->tablesExist([$table])) {
                continue;
            }

            $columns = $schemaManager->listTableColumns($table);

            if (isset($columns[self::LEGACY_FIELD], $columns['cud'])) {
                $tables[] = $table;
            }
        }

        return $tables;
    }
}
