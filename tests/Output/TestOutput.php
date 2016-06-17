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

/**
 * Class TestOutput.
 */
class TestOutput extends Output
{
    public $output = '';

    public function clear()
    {
        $this->output = '';
    }

    public function doWrite($message, $newline = false)
    {
        $this->output .= $message.($newline ? "\n" : '');
    }
}

/* EOF */
