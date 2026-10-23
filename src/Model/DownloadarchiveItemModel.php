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
 * @property int         $pid
 * @property int         $sorting
 * @property int         $tstamp
 * @property string      $title
 * @property string|null $description
 * @property string|null $singleSRC
 * @property bool        $protected
 * @property string|null $groups
 * @property bool        $addImage
 * @property string|null $imgSRC
 * @property string      $size
 * @property string|null $alt
 * @property string|null $caption
 * @property string      $useImage
 * @property bool        $published
 * @property string      $start
 * @property string      $stop
 */
class DownloadarchiveItemModel extends Model
{
    public const IMAGE_LINK_NONE = '0';

    public const IMAGE_LINK_LIGHTBOX = '1';

    public const IMAGE_LINK_DOWNLOAD = '2';

    protected static $strTable = 'tl_downloadarchiveitems';

    /**
     * @return Collection<self>|null
     */
    public static function findPublishedByPid(int $pid, array $options = []): Collection|null
    {
        $t = static::$strTable;
        $columns = ["$t.pid=?"];

        if (!static::isPreviewMode($options)) {
            $time = Date::floorToMinute();
            $columns[] = "$t.published=1 AND ($t.start='' OR $t.start<=$time) AND ($t.stop='' OR $t.stop>$time)";
        }

        $options['order'] ??= "$t.sorting";

        return static::findBy($columns, [$pid], $options);
    }

    public static function countByPid(int $pid): int
    {
        return static::countBy(['pid=?'], [$pid]);
    }
}
