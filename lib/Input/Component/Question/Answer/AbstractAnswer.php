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

abstract class AbstractAnswer implements AnswerInterface
{
    /**
     * @var Question
     */
    private $question;

    /**
     * @var mixed
     */
    private $answer;

    /**
     * @var bool
     */
    private $default;

    /**
     * @var bool
     */
    private $interactive;

    /**
     * @param Question   $question
     * @param mixed|null $answer
     * @param bool       $default
     * @param bool       $interactive
     */
    public function __construct(Question $question, $answer = null, bool $default = false, bool $interactive = true)
    {
        $this->question = $question;
        $this->answer = $answer;
        $this->default = $default;
        $this->interactive = $interactive;
    }

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
        return null !== $this->answer && !empty($this->answer);
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @return bool
     */
    public function isAnswerArray(): bool
    {
        return is_array($this->answer);
    }

    /**
     * @return bool
     */
    public function isAnswerScalar(): bool
    {
        return is_scalar($this->answer);
    }

    /**
     * @return bool
     */
    public function isAnswerBoolean(): bool
    {
        return is_bool($this->answer);
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
