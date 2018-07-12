<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Style;

use SR\Console\Input\Component\Question\Answer\AnswerInterface;
use SR\Console\Input\Component\Question\Answer\BooleanAnswer;
use SR\Console\Input\Component\Question\Answer\ChoiceAnswer;
use SR\Console\Input\Component\Question\Answer\MultipleChoiceAnswer;
use SR\Console\Input\Component\Question\Answer\StringAnswer;
use SR\Console\Output\Component\Action\AbstractAction;
use SR\Console\Output\Component\Block\Block;
use SR\Console\Output\Markup\Markup;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface StyleInterface extends OutputInterface
{
    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface;

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface;

    /**
     * @param int $verbosity
     *
     * @return self
     */
    public function setActiveVerbosity(int $verbosity): self;

    /**
     * @param int $verbosity
     *
     * @return self
     */
    public function setVerbosity($verbosity): self;

    /**
     * @return int
     */
    public function getVerbosity(): int;

    /**
     * @return bool
     */
    public function isQuiet(): bool;

    /**
     * @return bool
     */
    public function isVerbose(): bool;

    /**
     * @return bool
     */
    public function isVeryVerbose(): bool;

    /**
     * @return bool
     */
    public function isDebug(): bool;

    /**
     * @param bool $decorated
     *
     * @return self
     */
    public function setDecorated($decorated): self;

    /**
     * @return bool
     */
    public function isDecorated(): bool;

    /**
     * @param OutputFormatterInterface $formatter
     *
     * @return self
     */
    public function setFormatter(OutputFormatterInterface $formatter): self;

    /**
     * @return OutputFormatterInterface
     */
    public function getFormatter(): OutputFormatterInterface;

    /**
     * @param array[] $styles
     *
     * @return self
     */
    public function addFormatterStyles(array $styles): self;

    /**
     * @param int $verbosity
     *
     * @return self
     */
    public function environment(int $verbosity = OutputInterface::VERBOSITY_NORMAL): self;

    /**
     * @param string $lines
     * @param bool   $newLine
     * @param int    $options
     *
     * @return self
     */
    public function write($lines, $newLine = false, $options = 0): self;

    /**
     * @param array $lines
     * @param int   $options
     *
     * @return self
     */
    public function writeln($lines, $options = 0): self;

    /**
     * @param int $count
     *
     * @return self
     */
    public function newline(int $count = 1): self;

    /**
     * @param string|string[] $lines
     * @param mixed[]         $replacements
     *
     * @return self
     */
    public function text($lines, array $replacements = []): self;

    /**
     * @param string|string[] $lines
     * @param mixed[]         $replacements
     *
     * @return self
     */
    public function comment($lines, array $replacements = []): self;

    /**
     * @param string|string[] $lines
     * @param mixed[]         $replacements
     *
     * @return self
     */
    public function muted($lines, array $replacements = []): self;

    /**
     * @param array         $listing
     * @param \Closure|null $lineFormatter
     *
     * @return self
     */
    public function listing(array $listing, \Closure $lineFormatter = null): self;

    /**
     * @param array $definitions
     *
     * @return self
     */
    public function definitions(array $definitions): self;

    /**
     * @param int $length
     *
     * @return self
     */
    public function separator(int $length = null): self;

    /**
     * @param string $title
     *
     * @return self
     */
    public function title(string $title): self;

    /**
     * @param Application $application
     * @param array       ...$properties
     *
     * @return self
     */
    public function applicationTitle(Application $application, ...$properties): self;

    /**
     * @param string $section
     *
     * @return self
     */
    public function section(string $section): self;

    /**
     * @param string $section
     *
     * @return self
     */
    public function subSection(string $section): self;

    /**
     * @param string      $section
     * @param int         $iteration
     * @param int         $size
     * @param string|null $type
     *
     * @return self
     */
    public function enumeratedSection(string $section, int $iteration, int $size = null, string $type = null): self;

    /**
     * @param string|string[] $lines
     * @param string|null     $header
     * @param mixed[]         $replacements
     * @param int             $type
     * @param string|null     $prefix
     * @param Markup          $markup
     *
     * @return self
     */
    public function block($lines, string $header = null, array $replacements = [], int $type = Block::TYPE_MD, string $prefix = null, Markup $markup = null): self;

    /**
     * @param string|array $lines
     * @param mixed[]      $replacements
     * @param int          $type
     * @param string       $header
     *
     * @return self
     */
    public function info($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'INFO'): self;

    /**
     * @param string|array $lines
     * @param mixed[]      $replacements
     * @param int          $type
     * @param string       $header
     *
     * @return self
     */
    public function success($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'OK'): self;

    /**
     * @param string|array $lines
     * @param mixed[]      $replacements
     * @param int          $type
     * @param string       $header
     *
     * @return self
     */
    public function warning($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'WARN'): self;

    /**
     * @param string|array $lines
     * @param mixed[]      $replacements
     * @param int          $type
     * @param string       $header
     *
     * @return self
     */
    public function error($lines, array $replacements = [], int $type = Block::TYPE_LG, string $header = 'ERR'): self;

    /**
     * @param string|array $lines
     * @param mixed[]      $replacements
     * @param int          $type
     * @param string       $header
     *
     * @return self
     */
    public function critical($lines, array $replacements = [], int $type = Block::TYPE_LG, string $header = 'CRITICAL'): self;

    /**
     * @param string|null $actionText
     * @param string|null $prefixText
     * @param string|null $type
     * @param mixed       $typeArguments
     *
     * @return AbstractAction
     */
    public function action(string $actionText = null, string $prefixText = null, string $type = null, array $typeArguments = []): AbstractAction;

    /**
     * @param array $headers
     * @param array $rows
     *
     * @return self
     */
    public function table(array $headers, ...$rows): self;

    /**
     * @param array $headers
     * @param array $rows
     *
     * @return self
     */
    public function tableVertical(array $headers, ...$rows): self;

    /**
     * @param string        $question
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return AnswerInterface|StringAnswer
     */
    public function ask(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): StringAnswer;

    /**
     * @param string        $question
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return AnswerInterface|StringAnswer
     */
    public function askHidden(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): StringAnswer;

    /**
     * @param string        $question
     * @param bool          $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return AnswerInterface|BooleanAnswer
     */
    public function confirm(string $question, bool $default = true, \Closure $validator = null, \Closure $sanitizer = null): BooleanAnswer;

    /**
     * @param string        $question
     * @param array         $choices
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return AnswerInterface|ChoiceAnswer
     */
    public function choice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): ChoiceAnswer;

    /**
     * @param string        $question
     * @param array         $choices
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return AnswerInterface|ChoiceAnswer
     */
    public function hiddenChoice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): ChoiceAnswer;

    /**
     * @param string        $question
     * @param array         $choices
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return AnswerInterface|MultipleChoiceAnswer
     */
    public function multipleChoice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): MultipleChoiceAnswer;

    /**
     * @param int|null $steps
     *
     * @return ProgressBar
     */
    public function progress(int $steps = null): ProgressBar;

    /**
     * @param int|null $steps
     *
     * @return ProgressBar
     */
    public function progressStart(int $steps = null): ProgressBar;

    /**
     * @param ProgressBar $progress
     *
     * @return self
     */
    public function progressFinish(ProgressBar $progress): self;

    /**
     * @return self
     */
    public function prependText(): self;

    /**
     * @return self
     */
    public function prependBlock(): self;

    /**
     * @return int
     */
    public function getMaxLength(): int;

    /**
     * @param string $string
     * @param int    $padding
     * @param string $character
     * @param int    $orientation
     *
     * @return string
     */
    public function pad(string $string, int $padding, string $character = ' ', int $orientation = STR_PAD_BOTH): string;

    /**
     * @param string $string
     * @param string $character
     * @param int    $orientation
     *
     * @return string
     */
    public function padByTermWidth(string $string, string $character = ' ', int $orientation = STR_PAD_BOTH): string;

    /**
     * @param string $string
     *
     * @return int
     */
    public function strLength(string $string): int;
}
