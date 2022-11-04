<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Fixtures;

use Symfony\Component\Console\Application;

class ApplicationWithProps extends Application
{
    public function __construct(string $name = null, string $version = null)
    {
        parent::__construct($name ?? 'Test Application', $version ?? '0.0.0');
    }

    public function getAuthor(): string
    {
        return 'Author Name';
    }

    public function getAuthorEmail(): string
    {
        return 'name@domain.tld';
    }

    public function getLicense(): string
    {
        return 'MIT License';
    }

    public function getLicenseLink(): string
    {
        return 'https://opensource.org/licenses/MIT';
    }

    public function getGitHash(): string
    {
        return '7a6be6b5dbf2f2bb24a85f2c1dd261d29d254490';
    }
}
