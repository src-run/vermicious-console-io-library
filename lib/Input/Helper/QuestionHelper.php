<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Input\Helper;

use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class QuestionHelper
{
    use StyleAwareInternalTrait;

    /**
     * @var SymfonyQuestionHelper
     */
    private $helper;

    /**
     * @param StyleInterface $style
     */
    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
        $this->helper = new SymfonyQuestionHelper();
    }

    /**
     * @param string        $question
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return string
     */
    public function ask(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): string
    {
        $question = new Question($question, $default);
        $question->setValidator($validator);

        $result = $this->askQuestion($question);

        return null !== $sanitizer ? $sanitizer($result) : $result;
    }

    /**
     * @param string        $question
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return string
     */
    public function askHidden(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): string
    {
        $question = new Question($question, $default);
        $question->setHidden(true);
        $question->setValidator($validator);

        $result = $this->askQuestion($question);

        return null !== $sanitizer ? $sanitizer($result) : $result;
    }

    /**
     * @param string $question
     * @param bool   $default
     *
     * @return bool
     */
    public function confirm(string $question, bool $default = true): bool
    {
        return $this->askQuestion(new ConfirmationQuestion($question, $default));
    }

    /**
     * @param string      $question
     * @param array       $choices
     * @param string|null $default
     *
     * @return string
     */
    public function choice(string $question, array $choices, string $default = null): string
    {
        if (null !== $default) {
            $default = array_flip($choices)[$default];
        }

        return $this->askQuestion(new ChoiceQuestion($question, $choices, $default));
    }

    /**
     * @param Question $question
     *
     * @return string|bool
     */
    private function askQuestion(Question $question)
    {
        if ($this->style->getInput()->isInteractive()) {
            $this->style->prependBlock();
        }

        $answer = $this->helper->ask($this->style->getInput(), $this->style, $question);

        if ($this->style->getInput()->isInteractive()) {
            $this->style->newLine();
        }

        return $answer;
    }
}
