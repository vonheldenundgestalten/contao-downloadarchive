<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

use FelixPfeiffer\DownloadarchiveBundle\Model\DownloadarchiveItemModel;
use FelixPfeiffer\DownloadarchiveBundle\Model\DownloadarchiveModel;

// Back end module
$GLOBALS['BE_MOD']['content']['downloadarchive'] = [
    'tables' => ['tl_downloadarchive', 'tl_downloadarchiveitems'],
];

// Archive mounts of back end users and groups (tl_user(_group).downloadarchives)
$GLOBALS['TL_PERMISSIONS'][] = 'downloadarchives';

// Models
$GLOBALS['TL_MODELS']['tl_downloadarchive'] = DownloadarchiveModel::class;
$GLOBALS['TL_MODELS']['tl_downloadarchiveitems'] = DownloadarchiveItemModel::class;
