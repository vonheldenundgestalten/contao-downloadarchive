<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

namespace FelixPfeiffer\DownloadarchiveBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Member groups of a protected file, plus "guests" (-1) like core's protected
 * elements, which replaces the former "show to guests only" option.
 */
#[AsCallback(table: 'tl_downloadarchiveitems', target: 'fields.groups.options')]
class MemberGroupsOptionsListener
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function __invoke(): array
    {
        $options = [-1 => $this->translator->trans('MSC.guests', [], 'contao_default')];

        foreach ($this->connection->fetchAllAssociative('SELECT id, name FROM tl_member_group WHERE tstamp > 0 ORDER BY name') as $group) {
            $options[$group['id']] = $group['name'];
        }

        return $options;
    }
}
