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
    private AbstractProgressHelper $progress;

    public function __construct(AbstractProgressHelper $progress)
    {
        $this->progress = $progress;
    }

    public function render(): self
    {
        $this->progress->progressBar()->display();

        return $this;
    }

    public function message(string $name, string $message, mixed ...$replacements): self
    {
        $this->progress->progressBar()->setMessage($this->compileMessage($message, $replacements), $name);

        return $this;
    }

    public function context(string $message, mixed ...$replacements): self
    {
        $this->message('context', $message, ...$replacements);

        return $this;
    }

    public function action(string $message, mixed ...$replacements): self
    {
        $this->message('action', $message, ...$replacements);

        return $this;
    }

    public function progressHelper(): AbstractProgressHelper
    {
        return $this->progress;
    }

    private function compileMessage(string $message, array $replacements): string
    {
        if (0 === count($replacements)) {
            return $message;
        }

        return vsprintf($message, $replacements);
    }
}
