<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Input;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StreamableInputInterface;

class MemoryInput extends ArrayInput implements StreamableInputInterface
{
    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    /**
     * @param array $inputs
     */
    public function setInput(array $inputs)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, implode(PHP_EOL, $inputs));
        rewind($stream);

        $this->setStream($stream);
    }
}
