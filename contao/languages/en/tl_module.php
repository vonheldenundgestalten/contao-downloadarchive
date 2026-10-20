<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_LANG']['tl_module']['downloadarchive_legend'] = 'Download archive';
$GLOBALS['TL_LANG']['tl_module']['downloadmeta_legend'] = 'Meta data';

$GLOBALS['TL_LANG']['tl_module']['downloadarchive'] = ['Download archives', 'Select the download archives whose files are listed.'];
$GLOBALS['TL_LANG']['tl_module']['downloadSorting'] = ['Sort order', 'The files of all selected archives are sorted together. Leave empty to keep the order of the archives and their files.'];
$GLOBALS['TL_LANG']['tl_module']['downloadNumberOfItems'] = ['Total number of files', 'Limits the total number of files. Set to 0 to show all.'];
$GLOBALS['TL_LANG']['tl_module']['downloadShowMeta'] = ['Show meta data', 'Show the modification date and the file size.'];
$GLOBALS['TL_LANG']['tl_module']['downloadHideDate'] = ['Hide date', 'Show only the file size.'];

$GLOBALS['TL_LANG']['tl_module']['downloadSortingOptions']['sorting ASC'] = 'Sorting ascending';
$GLOBALS['TL_LANG']['tl_module']['downloadSortingOptions']['sorting DESC'] = 'Sorting descending';
$GLOBALS['TL_LANG']['tl_module']['downloadSortingOptions']['tstamp ASC'] = 'Date ascending';
$GLOBALS['TL_LANG']['tl_module']['downloadSortingOptions']['tstamp DESC'] = 'Date descending';
$GLOBALS['TL_LANG']['tl_module']['downloadSortingOptions']['title ASC'] = 'Title ascending';
$GLOBALS['TL_LANG']['tl_module']['downloadSortingOptions']['title DESC'] = 'Title descending';
