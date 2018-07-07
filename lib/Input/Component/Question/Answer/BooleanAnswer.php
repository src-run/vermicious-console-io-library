<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Input\Component\Question\Answer;

class BooleanAnswer extends AbstractAnswer
{
    /**
     * @return null|bool
     */
    public function getAnswer(): ?bool
    {
        return parent::getAnswer();
    }

    /**
     * @return string
     */
    public function stringifyAnswer(): string
    {
        return $this->getAnswer() ? 'true' : 'false';
    }

    /**
     * @return bool
     */
    public function isTrue(): bool
    {
        return true === $this->getAnswer();
    }

    /**
     * @return bool
     */
    public function isAffirmative(): bool
    {
        return $this->isTrue();
    }

    /**
     * @return bool
     */
    public function isFalse(): bool
    {
        return false === $this->getAnswer();
    }

    /**
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->isFalse();
    }
}
