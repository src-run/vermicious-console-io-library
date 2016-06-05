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

/**
 * Class StdOutInterface.
 */
interface StdOutInterface
{
    /**
     * @param string $message
     * @param mixed[] ...$replacements
     */
    public function write($message, ...$replacements);

    /**
     * @param string $message
     * @param mixed[] ...$replacements
     */
    public function writeLine($message, ...$replacements);

    /**
     * @param int $count
     */
    public function writeNewLine($count = 1);
}

/* EOF */
