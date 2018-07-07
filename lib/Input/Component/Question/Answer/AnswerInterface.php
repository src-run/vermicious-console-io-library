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

interface AnswerInterface
{
    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @return Question
     */
    public function getQuestion(): Question;

    /**
     * @return string
     */
    public function stringifyQuestion(): string;

    /**
     * @return bool
     */
    public function hasAnswer(): bool;

    /**
     * @return mixed
     */
    public function getAnswer();

    /**
     * @return string
     */
    public function stringifyAnswer(): string;

    /**
     * @return bool
     */
    public function isDefault(): bool;

    /**
     * @return bool
     */
    public function isInteractive(): bool;

    /**
     * @return bool
     */
    public function isHidden(): bool;

    /**
     * @return mixed
     */
    public function getDefault();
}
