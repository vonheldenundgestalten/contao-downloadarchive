<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('downloadarchive_legend', 'forms_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('downloadarchives', 'downloadarchive_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('extend', 'tl_user')
    ->applyToPalette('custom', 'tl_user')
;

// Archive mounts; create/delete permissions are part of tl_user.cud
$GLOBALS['TL_DCA']['tl_user']['fields']['downloadarchives'] = [
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_downloadarchive.title',
    'eval' => ['multiple' => true],
    'sql' => 'blob NULL',
    'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
];
