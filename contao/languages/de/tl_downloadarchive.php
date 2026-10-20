<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_LANG']['tl_downloadarchive']['title_legend'] = 'Titel';
$GLOBALS['TL_LANG']['tl_downloadarchive']['directory_legend'] = 'Verzeichnis einlesen';
$GLOBALS['TL_LANG']['tl_downloadarchive']['publish_legend'] = 'Veröffentlichung';

$GLOBALS['TL_LANG']['tl_downloadarchive']['title'] = ['Titel', 'Der Titel des Downloadarchivs. Er wird nur im Backend angezeigt.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['class'] = ['CSS-Klasse', 'Diese CSS-Klasse wird jeder Datei des Archivs hinzugefügt.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['loadDirectory'] = ['Verzeichnis einlesen', 'Beim Speichern alle Dateien eines Ordners hinzufügen. Das funktioniert nur, solange das Archiv noch keine Dateien enthält.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['dirSRC'] = ['Ordner', 'Der Ordner, dessen Dateien hinzugefügt werden.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['loadSubdir'] = ['Unterordner einbeziehen', 'Auch die Dateien aller Unterordner hinzufügen.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['extension'] = ['Dateiendungen', 'Nur Dateien mit diesen Endungen hinzufügen (kommagetrennt, z. B. "pdf,docx"). Leer lassen für alle Dateien.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['prefix'] = ['Titel-Präfix', 'Die Titel aus diesem Präfix und einer Nummer bilden (z. B. "Unsere Produkte 0001") statt aus den Dateinamen.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['publishAll'] = ['Alle Dateien veröffentlichen', 'Die hinzugefügten Dateien sofort veröffentlichen.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['published'] = ['Archiv veröffentlichen', 'Die Dateien des Archivs auf der Webseite anzeigen.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['start'] = ['Anzeigen ab', 'Das Archiv erst ab diesem Tag auf der Webseite anzeigen.'];
$GLOBALS['TL_LANG']['tl_downloadarchive']['stop'] = ['Anzeigen bis', 'Das Archiv nur bis zu diesem Tag auf der Webseite anzeigen.'];
