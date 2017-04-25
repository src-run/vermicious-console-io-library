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

class ApplicationWithProps extends Application
{
    /**
     * @param string|null $name
     * @param string|null $version
     */
    public function __construct(string $name = null, string $version = null)
    {
        parent::__construct($name ?: 'Test Application', $version ?: '0.0.0');
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return 'Author Name';
    }

    /**
     * @return string
     */
    public function getAuthorEmail(): string
    {
        return 'name@domain.tld';
    }

    /**
     * @return string
     */
    public function getLicense(): string
    {
        return 'MIT License';
    }

    /**
     * @return string
     */
    public function getLicenseLink(): string
    {
        return 'https://opensource.org/licenses/MIT';
    }

    /**
     * @return string
     */
    public function getGitHash(): string
    {
        return '7a6be6b5dbf2f2bb24a85f2c1dd261d29d254490';
    }
}
