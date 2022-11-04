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

trait AnswerTrait
{
    /**
     * @var Question
     */
    private $question;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var bool
     */
    private $default;

    /**
     * @var bool
     */
    private $interactive;

    public function __toString(): string
    {
        return $this->stringifyAnswer();
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function stringifyQuestion(): string
    {
        return $this->question->getQuestion();
    }

    public function hasAnswer(): bool
    {
        return null !== $this->result && (false === $this->result || !empty($this->result));
    }

    public function isMultiAnswer(): bool
    {
        return is_array($this->result);
    }

    public function isStringAnswer(): bool
    {
        return is_string($this->result);
    }

    public function isBooleanAnswer(): bool
    {
        return is_bool($this->result);
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function isInteractive(): bool
    {
        return $this->interactive;
    }

    public function isHidden(): bool
    {
        return $this->question->isHidden();
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->question->getDefault();
    }
}
