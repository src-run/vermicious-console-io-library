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
    public function getInput(): InputInterface;

    public function getOutput(): OutputInterface;

    public function setActiveVerbosity(int $verbosity): self;

    public function setVerbosity(int $level): self;

    public function getVerbosity(): int;

    public function isQuiet(): bool;

    public function isVerbose(): bool;

    public function isVeryVerbose(): bool;

    public function isDebug(): bool;

    public function setDecorated(bool $decorated): self;

    public function isDecorated(): bool;

    public function setFormatter(OutputFormatterInterface $formatter): self;

    public function getFormatter(): OutputFormatterInterface;

    public function addFormatterStyles(array $styles): self;

    public function environment(int $verbosity = OutputInterface::VERBOSITY_NORMAL): self;

    /**
     * @param string|iterable $lines
     */
    public function write($lines, bool $newline = false, int $options = 0): self;

    /**
     * @param string|iterable $lines
     */
    public function writeln($lines, int $options = 0): self;

    public function newline(int $count = 1): self;

    /**
     * @param string|iterable $lines
     */
    public function text($lines, array $replacements = []): self;

    /**
     * @param string|iterable $lines
     */
    public function comment($lines, array $replacements = []): self;

    /**
     * @param string|iterable $lines
     */
    public function muted($lines, array $replacements = []): self;

    public function listing(array $listing, \Closure $lineFormatter = null): self;

    public function definitions(array $definitions): self;

    public function separator(int $length = null): self;

    public function title(string $title): self;

    public function applicationTitle(Application $application, ...$properties): self;

    public function section(string $section): self;

    public function subSection(string $section): self;

    public function enumeratedSection(string $section, int $iteration, int $size = null, string $type = null): self;

    /**
     * @param string|iterable $lines
     */
    public function block($lines, string $header = null, array $replacements = [], int $type = Block::TYPE_MD, string $prefix = null, Markup $markup = null): self;

    /**
     * @param string|iterable $lines
     */
    public function info($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'INFO'): self;

    /**
     * @param string|iterable $lines
     */
    public function success($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'OK'): self;

    /**
     * @param string|iterable $lines
     */
    public function warning($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'WARN'): self;

    /**
     * @param string|iterable $lines
     */
    public function error($lines, array $replacements = [], int $type = Block::TYPE_LG, string $header = 'ERR'): self;

    /**
     * @param string|iterable $lines
     */
    public function critical($lines, array $replacements = [], int $type = Block::TYPE_LG, string $header = 'CRITICAL'): self;

    public function action(string $actionText = null, string $prefixText = null, string $type = null, array $typeArguments = []): AbstractAction;

    public function table(array $headers, ...$rows): self;

    public function tableVertical(array $headers, ...$rows): self;

    public function ask(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|StringAnswer;

    public function askHidden(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|StringAnswer;

    public function confirm(string $question, bool $default = true, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|BooleanAnswer;

    public function choice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null, iterable $completionValues = null): AnswerInterface|ChoiceAnswer;

    public function hiddenChoice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|ChoiceAnswer;

    public function multipleChoice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|MultipleChoiceAnswer;

    public function progress(int $steps = null): ProgressBar;

    public function progressStart(int $steps = null): ProgressBar;

    public function progressFinish(ProgressBar $progress): self;

    public function prependText(): self;

    public function prependBlock(): self;

    public function getMaxLength(): int;

    public function pad(string $string, int $padding, string $character = ' ', int $orientation = STR_PAD_BOTH): string;

    public function padByTermWidth(string $string, string $character = ' ', int $orientation = STR_PAD_BOTH): string;

    public function strLength(string $string): int;
}
