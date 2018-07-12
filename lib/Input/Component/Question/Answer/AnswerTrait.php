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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->stringifyAnswer();
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return string
     */
    public function stringifyQuestion(): string
    {
        return $this->question->getQuestion();
    }

    /**
     * @return bool
     */
    public function hasAnswer(): bool
    {
        return null !== $this->result && ($this->result === false || !empty($this->result));
    }

    /**
     * @return bool
     */
    public function isMultiAnswer(): bool
    {
        return is_array($this->result);
    }

    /**
     * @return bool
     */
    public function isStringAnswer(): bool
    {
        return is_string($this->result);
    }

    /**
     * @return bool
     */
    public function isBooleanAnswer(): bool
    {
        return is_bool($this->result);
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @return bool
     */
    public function isInteractive(): bool
    {
        return $this->interactive;
    }

    /**
     * @return bool
     */
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
