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

namespace VHUG\DownloadarchiveBundle\Security;

final class DownloadarchivePermissions
{
    /**
     * Access is granted if the current back end user can edit the given archive
     * (tl_user(_group).downloadarchives). Subject must be an archive ID.
     */
    public const USER_CAN_EDIT_ARCHIVE = 'contao_user.downloadarchives';

    /**
     * Back end module name ($GLOBALS['BE_MOD']['content']['downloadarchive']).
     */
    public const MODULE = 'downloadarchive';
}
