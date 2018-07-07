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

class MultipleChoiceAnswer extends AbstractAnswer
{
    /**
     * @param Question $question
     * @param string[] $choices
     * @param bool     $default
     * @param bool     $interactive
     */
    public function __construct(Question $question, array $choices = [], bool $default = false, bool $interactive = true)
    {
        parent::__construct(
            $question, self::hydrateChoiceAnswers($choices, $question, $default, $interactive), $default, $interactive
        );
    }

    /**
     * @return string
     */
    public function stringifyAnswer(): string
    {
        return implode(',', array_map(function (ChoiceAnswer $answer): string {
            return sprintf('"%s"', $answer->stringifyAnswer());
        }, $this->getAnswer()));
    }

    /**
     * @return ChoiceAnswer[]
     */
    public function getAnswer(): array
    {
        return parent::getAnswer() ?? [];
    }

    /**
     * @return null|AnswerInterface
     */
    public function firstAnswer(): ?AnswerInterface
    {
        $answers = $this->getAnswer();

        return count($answers) > 0 ? array_shift($answers) : null;
    }

    /**
     * @param string $search
     *
     * @return null|AnswerInterface
     */
    public function findAnswer(string $search): ?AnswerInterface
    {
        $answers = $this->getAnswer();

        array_filter($answers, function (ChoiceAnswer $answer) use ($search): bool {
            return $answer->getAnswer() === $search || $answer->getIndex() === $search;
        });

        if (count($answers) > 1) {
            throw new InvalidArgumentException('Search was ambiguous and returned %d results.', count($answers));
        }

        return $answers[0] ?? null;
    }

    /**
     * @return int
     */
    public function countAnswers(): int
    {
        return count($this->getAnswer());
    }

    /**
     * @param array    $choices
     * @param Question $question
     * @param bool     $default
     * @param bool     $interactive
     *
     * @return array
     */
    private static function hydrateChoiceAnswers(array $choices, Question $question, bool $default, bool $interactive): array
    {
        array_walk($choices, function (&$choice, $index) use ($question, $default, $interactive) {
            $choice = new ChoiceAnswer($question, $choice, $index, $default, $interactive);
        });

        return $choices;
    }
}
