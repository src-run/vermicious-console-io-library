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

class ChoiceAnswer extends StringAnswer
{
    /**
     * @var string|int|null
     */
    private $index;

    /**
     * @param Question   $question
     * @param mixed|null $answer
     * @param mixed|null $index
     * @param bool       $default
     * @param bool       $interactive
     */
    public function __construct(Question $question, $answer = null, $index = null, bool $default = false, bool $interactive = true)
    {
        parent::__construct($question, $answer, $default, $interactive);

        $this->index = $index;
    }

    /**
     * @return bool
     */
    public function hasIndex(): bool
    {
        return null !== $this->index;
    }

    /**
     * @return string|int|null
     */
    public function getIndex()
    {
        return $this->index;
    }
}
