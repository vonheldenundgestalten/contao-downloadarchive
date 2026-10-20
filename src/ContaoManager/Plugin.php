<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

namespace FelixPfeiffer\DownloadarchiveBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use FelixPfeiffer\DownloadarchiveBundle\FelixPfeifferDownloadarchiveBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * @internal
 */
class Plugin implements BundlePluginInterface, ConfigPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(FelixPfeifferDownloadarchiveBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['downloadarchive']),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig): void
    {
        $loader->load('@FelixPfeifferDownloadarchiveBundle/config/services.yaml');
    }
}
