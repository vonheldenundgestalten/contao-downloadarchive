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
 * Files can only be managed in archives of the user's archive mounts; moving a
 * file requires access to the old and the new archive.
 */
class DownloadarchiveItemAccessVoter extends AbstractDataContainerVoter
{
    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    protected function getTable(): string
    {
        return 'tl_downloadarchiveitems';
    }

    protected function hasAccess(TokenInterface $token, CreateAction|DeleteAction|ReadAction|UpdateAction $action): bool
    {
        if (!$this->accessDecisionManager->decide($token, [ContaoCorePermissions::USER_CAN_ACCESS_MODULE], DownloadarchivePermissions::MODULE)) {
            return false;
        }

        $pids = [$action instanceof CreateAction ? (int) $action->getNewPid() : (int) $action->getCurrentPid()];

        if ($action instanceof UpdateAction && ($newPid = (int) $action->getNewPid()) && $newPid !== $pids[0]) {
            $pids[] = $newPid;
        }

        foreach ($pids as $pid) {
            if (!$this->accessDecisionManager->decide($token, [DownloadarchivePermissions::USER_CAN_EDIT_ARCHIVE], $pid)) {
                return false;
            }
        }

        return true;
    }
}
