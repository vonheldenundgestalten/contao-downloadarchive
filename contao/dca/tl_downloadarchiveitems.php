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

use Contao\DataContainer;
use Contao\DC_Table;
use VHUG\DownloadarchiveBundle\Model\DownloadarchiveItemModel;

/*
 * The files of a download archive. The list label and the member group options
 * come from VHUG\DownloadarchiveBundle\EventListener\DataContainer\*.
 */
$GLOBALS['TL_DCA']['tl_downloadarchiveitems'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_downloadarchive',
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'tstamp' => 'index',
                'pid,published,start,stop' => 'index',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['sorting'],
            'panelLayout' => 'filter;search,limit',
            'defaultSearchField' => 'title',
            'headerFields' => ['title', 'tstamp', 'published'],
            'renderAsGrid' => true,
            'limitHeight' => 104,
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
    ],

    'palettes' => [
        '__selector__' => ['addImage', 'protected'],
        'default' => '{title_legend},title,description;{file_legend},singleSRC;{image_legend:hide},addImage;{protected_legend:hide},protected;{publish_legend},published,start,stop',
    ],

    'subpalettes' => [
        'addImage' => 'imgSRC,size,useImage,alt,caption',
        'protected' => 'groups',
    ],

    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'foreignKey' => 'tl_downloadarchive.title',
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'sorting' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'tstamp' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'title' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'description' => [
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'basicEntities' => true, 'tl_class' => 'clr'],
            'sql' => 'text NULL',
        ],
        'singleSRC' => [
            'inputType' => 'fileTree',
            'eval' => ['mandatory' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],
        'addImage' => [
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'imgSRC' => [
            'inputType' => 'fileTree',
            'eval' => ['mandatory' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'extensions' => '%contao.image.valid_extensions%', 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],
        'size' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['imgSize'],
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'platformOptions' => ['collation' => 'ascii_bin']],
        ],
        'useImage' => [
            'inputType' => 'select',
            'options' => [
                DownloadarchiveItemModel::IMAGE_LINK_NONE,
                DownloadarchiveItemModel::IMAGE_LINK_LIGHTBOX,
                DownloadarchiveItemModel::IMAGE_LINK_DOWNLOAD,
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_downloadarchiveitems']['useImageReference'],
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '0'",
        ],
        'alt' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'text NULL',
        ],
        'caption' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'w50'],
            'sql' => 'text NULL',
        ],
        'protected' => [
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'groups' => [
            'filter' => true,
            'inputType' => 'checkbox',
            'foreignKey' => 'tl_member_group.name',
            'eval' => ['mandatory' => true, 'multiple' => true],
            'sql' => 'blob NULL',
            'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
        ],
        'published' => [
            'toggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'start' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'stop' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
    ],
];
