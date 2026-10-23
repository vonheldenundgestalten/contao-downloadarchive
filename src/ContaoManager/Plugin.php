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

namespace VHUG\DownloadarchiveBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use VHUG\DownloadarchiveBundle\VhugDownloadarchiveBundle;

/**
 * @internal
 */
class Plugin implements BundlePluginInterface, ConfigPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(VhugDownloadarchiveBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace([
                    'downloadarchive',
                    'FelixPfeiffer\DownloadarchiveBundle\FelixPfeifferDownloadarchiveBundle',
                ]),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig): void
    {
        $loader->load('@VhugDownloadarchiveBundle/config/services.yaml');
    }
}
