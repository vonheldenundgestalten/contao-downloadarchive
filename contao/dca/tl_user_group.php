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

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('downloadarchive_legend', 'forms_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('downloadarchives', 'downloadarchive_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group')
;

// Archive mounts; create/delete permissions are part of tl_user_group.cud
$GLOBALS['TL_DCA']['tl_user_group']['fields']['downloadarchives'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user']['downloadarchives'],
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_downloadarchive.title',
    'eval' => ['multiple' => true],
    'sql' => 'blob NULL',
    'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
];
