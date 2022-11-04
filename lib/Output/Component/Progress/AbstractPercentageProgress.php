<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Progress;

use SR\Exception\Logic\InvalidArgumentException;

abstract class AbstractPercentageProgress extends AbstractProgressHelper
{
    private int $percent = 0;

    public function create(int $steps = null, string $context = null): AbstractProgressHelper|self
    {
        $this->ensureProgressStopped();

        if (null === $steps) {
            return parent::create(100, $context);
        }

        throw new InvalidArgumentException('Percent based progress helper is hard-coded to use 100 steps; do not pass step count to "%s"!', __METHOD__);
    }

    public function step(int $count = 1): AbstractProgressHelper|self
    {
        $this->ensureProgressStarted();
        $this->percent += $count;

        return parent::step($count);
    }

    public function percent(int $percent): self
    {
        return $this->step($percent - $this->percent);
    }
}
