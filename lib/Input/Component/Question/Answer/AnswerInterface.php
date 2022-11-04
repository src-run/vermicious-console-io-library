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
    public function __toString(): string;

    public function getQuestion(): Question;

    public function stringifyQuestion(): string;

    public function hasAnswer(): bool;

    /**
     * @return mixed
     */
    public function getAnswer();

    public function stringifyAnswer(): string;

    public function isDefault(): bool;

    public function isInteractive(): bool;

    public function isHidden(): bool;

    /**
     * @return mixed
     */
    public function getDefault();
}
