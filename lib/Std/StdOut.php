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
 * Class StdOut.
 */
final class StdOut extends AbstractStdOut
{
    /**
     * @return StreamOutput
     */
    public function getDefaultOutputStream()
    {
        $stream = @fopen('php://stdout', 'w') ?: fopen('php://output', 'w');

        return new StreamOutput($stream);
    }
}

/* EOF */
