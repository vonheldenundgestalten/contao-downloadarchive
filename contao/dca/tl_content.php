<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

use FelixPfeiffer\DownloadarchiveBundle\Controller\ContentElement\DownloadarchiveController;
use FelixPfeiffer\DownloadarchiveBundle\Downloadarchive\DownloadarchiveRenderer;

$GLOBALS['TL_DCA']['tl_content']['palettes'][DownloadarchiveController::TYPE] =
    '{type_legend},type,headline;{text_legend},downloadText'
    .';{downloadarchive_legend},downloadarchive,downloadSorting,downloadGroupByArchive,downloadNumberOfItems,perPage'
    .';{downloadmeta_legend:hide},downloadShowMeta,downloadHideDate'
    .';{template_legend:hide},customTpl'
    .';{protected_legend:hide},protected'
    .';{expert_legend:hide},cssID'
    .';{invisible_legend:hide},invisible,start,stop'
;

$GLOBALS['TL_DCA']['tl_content']['fields']['downloadarchive'] = [
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_downloadarchive.title',
    'eval' => ['multiple' => true, 'mandatory' => true],
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['downloadSorting'] = [
    'inputType' => 'select',
    'options' => DownloadarchiveRenderer::SORTING_OPTIONS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['downloadSortingOptions'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(25) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['downloadNumberOfItems'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'tl_class' => 'w50'],
    'sql' => 'smallint(5) unsigned NOT NULL default 0',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['downloadShowMeta'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => true],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['downloadHideDate'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['downloadGroupByArchive'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

// Optional intro text above the list (core's "text" field is mandatory)
$GLOBALS['TL_DCA']['tl_content']['fields']['downloadText'] = [
    'search' => true,
    'inputType' => 'textarea',
    'eval' => ['rte' => 'tinyMCE', 'basicEntities' => true, 'helpwizard' => true, 'tl_class' => 'clr'],
    'explanation' => 'insertTags',
    'sql' => 'mediumtext NULL',
];
