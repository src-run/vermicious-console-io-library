<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Progress\Message;

use SR\Console\Output\Component\Progress\AbstractProgressHelper;

class ProgressMessageHelper
{
    /**
     * @var AbstractProgressHelper
     */
    private $progress;

    /**
     * @param AbstractProgressHelper $progress
     */
    public function __construct(AbstractProgressHelper $progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return self
     */
    public function render(): self
    {
        $this->progress->progressBar()->display();

        return $this;
    }

    /**
     * @param string  $name
     * @param string  $message
     * @param mixed[] ...$replacements
     *
     * @return self
     */
    public function message(string $name, string $message, ...$replacements): self
    {
        $this->progress->progressBar()->setMessage($this->compileMessage($message, $replacements), $name);

        return $this;
    }

    /**
     * @param string  $message
     * @param mixed[] ...$replacements
     *
     * @return self
     */
    public function context(string $message, ...$replacements): self
    {
        $this->message('context', $message, ...$replacements);

        return $this;
    }

    /**
     * @param string  $message
     * @param mixed[] ...$replacements
     *
     * @return self
     */
    public function action(string $message, ...$replacements): self
    {
        $this->message('action', $message, ...$replacements);

        return $this;
    }

    /**
     * @return AbstractProgressHelper
     */
    public function progressHelper(): AbstractProgressHelper
    {
        return $this->progress;
    }

    /**
     * @param string  $message
     * @param mixed[] $replacements
     *
     * @return string
     */
    private function compileMessage(string $message, $replacements): string
    {
        if (0 === count($replacements)) {
            return $message;
        }

        return vsprintf($message, $replacements);
    }
}
