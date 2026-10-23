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
use Doctrine\DBAL\Types\StringType;

/**
 * The checkboxes used to be char(1) columns with '' for "off", which the
 * schema update cannot convert to boolean columns on its own.
 */
class BooleanFieldsMigration extends AbstractMigration
{
    /**
     * Table => field => default.
     */
    private const FIELDS = [
        'tl_downloadarchive' => [
            'loadDirectory' => 0,
            'loadSubdir' => 0,
            'publishAll' => 0,
            'published' => 1,
        ],
        'tl_downloadarchiveitems' => [
            'protected' => 0,
            'addImage' => 0,
            'published' => 0,
        ],
        'tl_content' => [
            'downloadShowMeta' => 1,
            'downloadHideDate' => 0,
        ],
        'tl_module' => [
            'downloadShowMeta' => 1,
            'downloadHideDate' => 0,
        ],
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        return [] !== $this->getFieldsToMigrate();
    }

    public function run(): MigrationResult
    {
        foreach ($this->getFieldsToMigrate() as [$table, $field, $default]) {
            $this->connection->executeStatement("UPDATE $table SET `$field` = '0' WHERE `$field` IS NULL OR `$field` != '1'");
            $this->connection->executeStatement("ALTER TABLE $table CHANGE `$field` `$field` tinyint(1) NOT NULL default $default");
        }

        return $this->createResult(true);
    }

    /**
     * @return list<array{string, string, int}>
     */
    private function getFieldsToMigrate(): array
    {
        $schemaManager = $this->connection->createSchemaManager();
        $fields = [];

        foreach (self::FIELDS as $table => $defaults) {
            if (!$schemaManager->tablesExist([$table])) {
                continue;
            }

            $columns = $schemaManager->listTableColumns($table);

            foreach ($defaults as $field => $default) {
                $column = $columns[strtolower($field)] ?? null;

                if ($column && $column->getType() instanceof StringType) {
                    $fields[] = [$table, $field, $default];
                }
            }
        }

        return $fields;
    }
}
