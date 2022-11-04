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

use SR\Console\Output\Exception\InvalidArgumentException;
use Symfony\Component\Console\Question\Question;

class MultipleChoiceAnswer implements AnswerInterface, \Countable
{
    use AnswerTrait;

    /**
     * @param mixed|null $choices
     */
    public function __construct(Question $question, array $choices = [], bool $default = false, bool $interactive = true)
    {
        $this->question = $question;
        $this->default = $default;
        $this->interactive = $interactive;
        $this->result = array_map(function (string $index) use ($choices, $question, $default, $interactive): ChoiceAnswer {
            return new ChoiceAnswer($question, $choices[$index], $index, $default, $interactive);
        }, array_keys($choices));
    }

    public function stringifyAnswer(): string
    {
        return implode(',', array_map(function (ChoiceAnswer $a): string {
            return sprintf('"%s"', $a->stringifyAnswer());
        }, $this->getAnswer()));
    }

    /**
     * @return ChoiceAnswer[]
     */
    public function getAnswer(): array
    {
        return $this->result ?? [];
    }

    public function firstAnswer(): ?AnswerInterface
    {
        $answers = $this->getAnswer();

        return count($answers) > 0 ? array_shift($answers) : null;
    }

    /**
     * @param string|\Closure $search
     *
     * @return AnswerInterface|ChoiceAnswer|null
     */
    public function findAnswer($search): ?ChoiceAnswer
    {
        if (count($answers = $this->filterAnswers($search)) > 1) {
            throw new InvalidArgumentException('Search was ambiguous and returned %d results.', count($answers));
        }

        return array_shift($answers) ?? null;
    }

    /**
     * @param string|\Closure $search
     *
     * @return AnswerInterface[]|ChoiceAnswer[]
     */
    public function filterAnswers($search): array
    {
        return array_filter($this->getAnswer(), $search instanceof \Closure ? $search : function (ChoiceAnswer $a) use ($search) {
            return $a->getAnswer() === $search || $a->getIndex() === $search;
        });
    }

    public function count(): int
    {
        return count($this->getAnswer());
    }
}
