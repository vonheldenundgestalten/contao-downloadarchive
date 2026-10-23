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

namespace VHUG\DownloadarchiveBundle\Model;

use Contao\Date;
use Contao\Model;
use Contao\Model\Collection;

/**
 * @property int         $id
 * @property int         $tstamp
 * @property string      $title
 * @property string      $class
 * @property bool        $loadDirectory
 * @property string|null $dirSRC
 * @property bool        $loadSubdir
 * @property string      $extension
 * @property string      $prefix
 * @property bool        $publishAll
 * @property bool        $published
 * @property string      $start
 * @property string      $stop
 */
class DownloadarchiveModel extends Model
{
    protected static $strTable = 'tl_downloadarchive';

    /**
     * Find the published archives by their IDs, in the given order.
     *
     * @param array<int|string> $ids
     *
     * @return Collection<self>|null
     */
    public static function findPublishedByIds(array $ids, array $options = []): Collection|null
    {
        $ids = array_values(array_filter(array_map(intval(...), $ids)));

        if ([] === $ids) {
            return null;
        }

        $t = static::$strTable;
        $columns = ["$t.id IN(".implode(',', $ids).')'];

        if (!static::isPreviewMode($options)) {
            $time = Date::floorToMinute();
            $columns[] = "$t.published=1 AND ($t.start='' OR $t.start<=$time) AND ($t.stop='' OR $t.stop>$time)";
        }

        $options['order'] ??= 'FIELD('.$t.'.id,'.implode(',', $ids).')';

        return static::findBy($columns, null, $options);
    }
}
