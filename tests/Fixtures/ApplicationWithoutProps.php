<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) 2016 Rob Frawley 2nd(rmf) <rmf AT src DOT run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Fixtures;

use Symfony\Component\Console\Application;

class ApplicationWithoutProps extends Application
{
    /**
     * @param string|null $name
     * @param string|null $version
     */
    public function __construct(string $name = null, string $version = null)
    {
        parent::__construct($name ?: 'Test Application', $version);
    }
}
