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

use Symfony\Component\Console\Question\Question;

class BooleanAnswer implements AnswerInterface
{
    use AnswerTrait;

    /**
     * @param Question   $question
     * @param mixed|null $answer
     * @param bool       $default
     * @param bool       $interactive
     */
    public function __construct(Question $question, $answer = null, bool $default = false, bool $interactive = true)
    {
        $this->question = $question;
        $this->result = $answer;
        $this->default = $default;
        $this->interactive = $interactive;
    }

    /**
     * @return null|bool
     */
    public function getAnswer(): ?bool
    {
        return $this->result;
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
    public function isFalse(): bool
    {
        return false === $this->getAnswer();
    }
}
