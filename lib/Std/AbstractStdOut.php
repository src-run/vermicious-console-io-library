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

use SR\Console\Output\OutputAwareTrait;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractStdOut.
 */
abstract class AbstractStdOut implements StdOutInterface
{
    use OutputAwareTrait;

    /**
     * @param OutputInterface|null $output
     */
    public function __construct(OutputInterface $output = null)
    {
        $this->setOutput($output ?: $this->getDefaultOutputStream());
    }

    /**
     * @return OutputInterface
     */
    abstract public function getDefaultOutputStream();

    /**
     * @param string $message
     * @param mixed[] ...$replacements
     */
    public function write($message, ...$replacements)
    {
        $this->output->write(sprintf($message, ...$replacements));
    }

    /**
     * @param string $message
     * @param mixed[] ...$replacements
     */
    public function writeLine($message, ...$replacements)
    {
        $this->write($message, ...$replacements);
        $this->writeNewLine();
    }

    /**
     * @param int $count
     */
    public function writeNewLine($count = 1)
    {
        foreach (range(1, $count) as $i) {
            $this->output->write('', true);
        }
    }
}

/* EOF */
