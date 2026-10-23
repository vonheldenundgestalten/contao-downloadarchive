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

namespace VHUG\DownloadarchiveBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VhugDownloadarchiveBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
