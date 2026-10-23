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

namespace VHUG\DownloadarchiveBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VHUG\DownloadarchiveBundle\Downloadarchive\DownloadarchiveRenderer;

/**
 * Lists the files of one or more download archives.
 */
#[AsContentElement(self::TYPE, category: 'files')]
class DownloadarchiveController extends AbstractContentElementController
{
    public const TYPE = 'downloadarchive';

    public function __construct(private readonly DownloadarchiveRenderer $renderer)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ($this->isBackendScope($request)) {
            $this->renderer->renderBackendPreview($template, $model);
        } else {
            $this->renderer->render($template, $model, $request, $this->getPageModel());
        }

        return $template->getResponse();
    }
}
