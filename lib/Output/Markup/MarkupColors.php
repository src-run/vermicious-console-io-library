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

interface MarkupColors
{
    /**
     * @var string
     */
    public const C_DEFAULT = 'default';

    /**
     * @var string
     */
    public const C_BLACK = 'black';

    /**
     * @var string
     */
    public const C_RED = 'red';

    /**
     * @var string
     */
    public const C_GREEN = 'green';

    /**
     * @var string
     */
    public const C_YELLOW = 'yellow';

    /**
     * @var string
     */
    public const C_BLUE = 'blue';

    /**
     * @var string
     */
    public const C_MAGENTA = 'magenta';

    /**
     * @var string
     */
    public const C_CYAN = 'cyan';

    /**
     * @var string
     */
    public const C_WHITE = 'white';
}
