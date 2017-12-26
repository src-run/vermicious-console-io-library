<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) 2016 Rob Frawley 2nd(rmf) <rmf AT src DOT run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Output;

use Symfony\Component\Console\Output\Output;

class TestOutput extends Output
{
    /**
     * @var string
     */
    public $buffer = '';

    /**
     * @param string $message
     * @param bool   $newline
     */
    public function doWrite($message, $newline = false)
    {
        $this->buffer .= $message.($newline ? "\n" : '');
    }
}
