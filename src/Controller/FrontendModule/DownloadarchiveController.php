<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

namespace FelixPfeiffer\DownloadarchiveBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use FelixPfeiffer\DownloadarchiveBundle\Downloadarchive\DownloadarchiveRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lists the files of one or more download archives.
 */
#[AsFrontendModule(self::TYPE, category: 'application')]
class DownloadarchiveController extends AbstractFrontendModuleController
{
    public const TYPE = 'downloadarchive';

    public function __construct(private readonly DownloadarchiveRenderer $renderer)
    {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $this->renderer->render($template, $model, $request, $this->getPageModel());

        return $template->getResponse();
    }
}
