<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Std;

use Symfony\Component\Console\Output\StreamOutput;

/**
 * Class StdErr.
 */
final class StdErr extends AbstractStdOut
{
    /**
     * @return StreamOutput
     */
    public function getDefaultOutputStream()
    {
        $stream = @fopen('php://stderr', 'w') ?: fopen('php://output', 'w');

        return new StreamOutput($stream);
    }
}

/* EOF */
