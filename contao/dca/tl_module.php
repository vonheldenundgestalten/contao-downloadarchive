<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

use FelixPfeiffer\DownloadarchiveBundle\Controller\FrontendModule\DownloadarchiveController;
use FelixPfeiffer\DownloadarchiveBundle\Downloadarchive\DownloadarchiveRenderer;

$GLOBALS['TL_DCA']['tl_module']['palettes'][DownloadarchiveController::TYPE] =
    '{title_legend},name,headline,type'
    .';{downloadarchive_legend},downloadarchive,downloadSorting,downloadGroupByArchive,downloadNumberOfItems,perPage'
    .';{downloadmeta_legend:hide},downloadShowMeta,downloadHideDate'
    .';{template_legend:hide},customTpl'
    .';{protected_legend:hide},protected'
    .';{expert_legend:hide},cssID'
;

$GLOBALS['TL_DCA']['tl_module']['fields']['downloadarchive'] = [
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_downloadarchive.title',
    'eval' => ['multiple' => true, 'mandatory' => true],
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['downloadSorting'] = [
    'inputType' => 'select',
    'options' => DownloadarchiveRenderer::SORTING_OPTIONS,
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['downloadSortingOptions'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(25) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['downloadNumberOfItems'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'tl_class' => 'w50'],
    'sql' => 'smallint(5) unsigned NOT NULL default 0',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['downloadShowMeta'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['downloadHideDate'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['downloadGroupByArchive'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];
