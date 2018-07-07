<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Markup;

interface MarkupOptions
{
    /**
     * @var string
     */
    public const O_BOLD = 'bold';

    /**
     * @var string
     */
    public const O_UNDERSCORE = 'underscore';

    /**
     * @var string
     */
    public const O_BLINK = 'blink';

    /**
     * @var string
     */
    public const O_REVERSE = 'reverse';
}
