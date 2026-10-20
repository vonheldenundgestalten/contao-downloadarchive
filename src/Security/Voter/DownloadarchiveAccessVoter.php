<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

namespace FelixPfeiffer\DownloadarchiveBundle\Security\Voter;

use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\Security\DataContainer\CreateAction;
use Contao\CoreBundle\Security\DataContainer\DeleteAction;
use Contao\CoreBundle\Security\DataContainer\ReadAction;
use Contao\CoreBundle\Security\DataContainer\UpdateAction;
use Contao\CoreBundle\Security\Voter\DataContainer\AbstractDataContainerVoter;
use FelixPfeiffer\DownloadarchiveBundle\Security\DownloadarchivePermissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Back end users may only read and change the archives in their archive
 * mounts. Whether they may create or delete archives at all is checked by
 * core's DcaPermissionVoter (tl_user.cud).
 */
class DownloadarchiveAccessVoter extends AbstractDataContainerVoter
{
    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    protected function getTable(): string
    {
        return 'tl_downloadarchive';
    }

    protected function hasAccess(TokenInterface $token, CreateAction|DeleteAction|ReadAction|UpdateAction $action): bool
    {
        if (!$this->accessDecisionManager->decide($token, [ContaoCorePermissions::USER_CAN_ACCESS_MODULE], DownloadarchivePermissions::MODULE)) {
            return false;
        }

        if ($action instanceof CreateAction) {
            return true;
        }

        return $this->accessDecisionManager->decide($token, [DownloadarchivePermissions::USER_CAN_EDIT_ARCHIVE], (int) $action->getCurrentId());
    }
}
