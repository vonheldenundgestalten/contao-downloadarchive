<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

namespace FelixPfeiffer\DownloadarchiveBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FelixPfeifferDownloadarchiveBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
