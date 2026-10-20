<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_LANG']['tl_downloadarchive']['title_legend'] = 'Title';
$GLOBALS['TL_LANG']['tl_downloadarchive']['directory_legend'] = 'Read a directory';
$GLOBALS['TL_LANG']['tl_downloadarchive']['publish_legend'] = 'Publish settings';

$GLOBALS['TL_LANG']['tl_downloadarchive']['title'] = ['Title', 'The title of the download archive. It is only shown in the back end.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['class'] = ['CSS class', 'This CSS class is added to every file of the archive.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['loadDirectory'] = ['Read a directory', 'Add all files of a folder when saving. This only works as long as the archive has no files.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['dirSRC'] = ['Folder', 'The folder whose files are added.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['loadSubdir'] = ['Include subfolders', 'Also add the files of all subfolders.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['extension'] = ['File extensions', 'Only add files with these extensions (comma separated, e.g. "pdf,docx"). Leave empty for all files.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['prefix'] = ['Title prefix', 'Generate the titles from this prefix and a number (e.g. "Our products 0001") instead of using the file names.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['publishAll'] = ['Publish all files', 'Publish the added files right away.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['published'] = ['Publish archive', 'Show the files of the archive on the website.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['start'] = ['Show from', 'Do not show the archive on the website before this date.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['stop'] = ['Show until', 'Do not show the archive on the website on and after this date.'];
