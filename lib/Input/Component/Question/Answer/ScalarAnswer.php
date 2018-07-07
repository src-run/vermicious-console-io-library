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

class ScalarAnswer extends AbstractAnswer
{
    /**
     * @return null|string
     */
    public function getAnswer(): ?string
    {
        return parent::getAnswer();
    }

    /**
     * @return string
     */
    public function stringifyAnswer(): string
    {
        return (string) ($this->getAnswer() ?? '');
    }
}
