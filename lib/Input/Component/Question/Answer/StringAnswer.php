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

class StringAnswer implements AnswerInterface
{
    use AnswerTrait;

    /**
     * @param mixed|null $answer
     */
    public function __construct(Question $question, $answer = null, bool $default = false, bool $interactive = true)
    {
        $this->question = $question;
        $this->result = $answer;
        $this->default = $default;
        $this->interactive = $interactive;
    }

    public function getAnswer(): ?string
    {
        return $this->result;
    }

    public function stringifyAnswer(): string
    {
        return (string) ($this->getAnswer() ?? '');
    }

    public function length(): int
    {
        return mb_strlen($this->getAnswer());
    }
}
