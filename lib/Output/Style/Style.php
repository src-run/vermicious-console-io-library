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
use SR\Console\Input\Component\Question\QuestionHelper;
use SR\Console\Input\InputAwareTrait;
use SR\Console\Output\Component\Action\AbstractAction;
use SR\Console\Output\Component\Action\ActionFactory;
use SR\Console\Output\Component\Block\Block;
use SR\Console\Output\Component\Header\SectionHeader;
use SR\Console\Output\Component\Header\TitleHeader;
use SR\Console\Output\Component\Listing\DefinitionList;
use SR\Console\Output\Component\Listing\SimpleList;
use SR\Console\Output\Component\Table\HorizontalTable;
use SR\Console\Output\Component\Table\VerticalTable;
use SR\Console\Output\Component\Text\Text;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\OutputAwareTrait;
use SR\Console\Output\Utility\Terminal\Terminal;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Style implements StyleInterface
{
    use InputAwareTrait;
    use OutputAwareTrait;

    private BufferedOutput $buffer;

    private int $verbosity;

    private int $maxLength;

    public function __construct(InputInterface $input, OutputInterface $output, int $lineLength = 320)
    {
        $this->maxLength = $lineLength;
        $this->verbosity = self::VERBOSITY_NORMAL;

        $this->setInput($input);
        $this->setOutput($output);
        $this->setupBuffer($output);

        $this->addFormatterStyles([
            'hl' => ['magenta'],
            'ok' => ['black', 'green'],
            'em' => [null, null, ['bold']],
        ]);
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function setActiveVerbosity(int $verbosity): StyleInterface
    {
        $this->verbosity = $verbosity;

        return $this;
    }

    public function setVerbosity(int $level): StyleInterface
    {
        $this->output->setVerbosity($level);

        return $this;
    }

    public function getVerbosity(): int
    {
        return $this->output->getVerbosity();
    }

    public function isQuiet(): bool
    {
        return $this->output->isQuiet();
    }

    public function isVerbose(): bool
    {
        return $this->output->isVerbose();
    }

    public function isVeryVerbose(): bool
    {
        return $this->output->isVeryVerbose();
    }

    public function isDebug(): bool
    {
        return $this->output->isDebug();
    }

    public function setDecorated(bool $decorated): StyleInterface
    {
        $this->output->setDecorated($decorated);

        return $this;
    }

    public function isDecorated(): bool
    {
        return $this->output->isDecorated();
    }

    public function setFormatter(OutputFormatterInterface $formatter): StyleInterface
    {
        $this->output->setFormatter($formatter);

        return $this;
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return $this->output->getFormatter();
    }

    public function addFormatterStyles(array $styles): StyleInterface
    {
        foreach ($styles as $name => $attributes) {
            $this->getFormatter()->setStyle($name, new OutputFormatterStyle(...$attributes));
        }

        return $this;
    }

    public function environment(int $verbosity = OutputInterface::VERBOSITY_NORMAL): StyleInterface
    {
        $environment = clone $this;
        $environment->setActiveVerbosity($verbosity);

        return $environment;
    }

    /**
     * @param string|iterable $lines
     */
    public function write($lines, bool $newline = false, int $options = 0): StyleInterface
    {
        $this->output->write($lines, $newline, $options | $this->verbosity);
        $this->buffer->write($this->reduceBuffer((array) $lines), $newline, $options | $this->verbosity);

        return $this;
    }

    /**
     * @param string|iterable $lines
     */
    public function writeln($lines, int $options = 0): StyleInterface
    {
        $this->output->writeln($lines, $options | $this->verbosity);
        $this->buffer->writeln($this->reduceBuffer($lines), $options | $this->verbosity);

        return $this;
    }

    public function newline(int $count = 1): StyleInterface
    {
        $this->output->write($output = str_repeat(PHP_EOL, $count), false, $this->verbosity);
        $this->buffer->write($output, false, $this->verbosity);

        return $this;
    }

    /**
     * @param string|string[] $lines
     */
    public function text($lines, array $replacements = []): StyleInterface
    {
        (new Text($this))->text($lines, $replacements);

        return $this;
    }

    /**
     * @param string|string[] $lines
     */
    public function comment($lines, array $replacements = []): StyleInterface
    {
        (new Text($this))->comment($lines, $replacements);

        return $this;
    }

    /**
     * @param string|string[] $lines
     */
    public function muted($lines, array $replacements = []): StyleInterface
    {
        (new Text($this))->muted($lines, $replacements);

        return $this;
    }

    public function listing(array $listing, \Closure $lineFormatter = null): StyleInterface
    {
        (new SimpleList($this, $lineFormatter))->listing($listing);

        return $this;
    }

    public function definitions(array $definitions): StyleInterface
    {
        (new DefinitionList($this))->definitions($definitions);

        return $this;
    }

    /**
     * @param int $length
     */
    public function separator(int $length = null, string $character = '-'): StyleInterface
    {
        (new Text($this))->separator($length, $character);

        return $this;
    }

    public function title(string $title): StyleInterface
    {
        (new TitleHeader($this))->title($title);

        return $this;
    }

    /**
     * @param array ...$properties
     */
    public function applicationTitle(Application $application, ...$properties): StyleInterface
    {
        (new TitleHeader($this))->applicationTitle($application, ...$properties);

        return $this;
    }

    public function section(string $section): StyleInterface
    {
        (new SectionHeader($this))->section($section);

        return $this;
    }

    public function subSection(string $section): StyleInterface
    {
        (new SectionHeader($this))->subSection($section);

        return $this;
    }

    public function enumeratedSection(string $section, int $iteration, int $size = null, string $type = null): StyleInterface
    {
        (new SectionHeader($this))->enumeratedSection($section, $iteration, $size, $type);

        return $this;
    }

    /**
     * @param string|array $lines
     */
    public function block($lines, string $header = null, array $replacements = [], int $type = Block::TYPE_MD, string $prefix = null, Markup $markup = null): StyleInterface
    {
        (new Block($this, $type))->write((array) $lines, $header, $replacements, $prefix, $markup);

        return $this;
    }

    /**
     * @param string|array $lines
     */
    public function info($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'INFO'): StyleInterface
    {
        return $this->block((array) $lines, $header, $replacements, $type, '--', new Markup('white', 'blue'));
    }

    /**
     * @param string|array $lines
     */
    public function success($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'OK'): StyleInterface
    {
        return $this->block((array) $lines, $header, $replacements, $type, '||', new Markup('black', 'green'));
    }

    /**
     * @param string|array $lines
     */
    public function warning($lines, array $replacements = [], int $type = Block::TYPE_MD, string $header = 'WARN'): StyleInterface
    {
        return $this->block((array) $lines, $header, $replacements, $type, '##', new Markup('black', 'yellow'));
    }

    /**
     * @param string|array $lines
     */
    public function error($lines, array $replacements = [], int $type = Block::TYPE_LG, string $header = 'ERR'): StyleInterface
    {
        return $this->block((array) $lines, $header, $replacements, $type, '!!', new Markup('white', 'red'));
    }

    /**
     * @param string|array $lines
     */
    public function critical($lines, array $replacements = [], int $type = Block::TYPE_LG, string $header = 'CRITICAL'): StyleInterface
    {
        return $this->block((array) $lines, $header, $replacements, $type, '**', new Markup('white', 'red'));
    }

    public function action(string $actionText = null, string $prefixText = null, string $type = null, array $typeArguments = []): AbstractAction
    {
        $action = ActionFactory::create($type, ...$typeArguments);
        $action->setStyle($this);

        if (null !== $prefixText) {
            $action->prefix($prefixText);
        }

        if (null !== $actionText) {
            $action->action($actionText);
        }

        return $action;
    }

    public function table(array $headers, ...$rows): StyleInterface
    {
        (new HorizontalTable($this))->write($headers, ...$rows);

        return $this;
    }

    public function tableVertical(array $headers, ...$rows): StyleInterface
    {
        (new VerticalTable($this))->write($headers, ...$rows);

        return $this;
    }

    public function ask(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|StringAnswer
    {
        return (new QuestionHelper($this))->question($question, $default, $validator, $sanitizer);
    }

    public function askHidden(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|StringAnswer
    {
        return (new QuestionHelper($this))->hiddenQuestion($question, $default, $validator, $sanitizer);
    }

    public function confirm(string $question, bool $default = true, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|BooleanAnswer
    {
        return (new QuestionHelper($this))->confirm($question, $default, $validator, $sanitizer);
    }

    public function choice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null, iterable $completionValues = null): AnswerInterface|ChoiceAnswer
    {
        return (new QuestionHelper($this))->choice($question, $choices, $default, false, $validator, $sanitizer, $completionValues);
    }

    public function hiddenChoice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|ChoiceAnswer
    {
        return (new QuestionHelper($this))->hiddenChoice($question, $choices, $default, false, $validator, $sanitizer);
    }

    public function multipleChoice(string $question, array $choices, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): AnswerInterface|MultipleChoiceAnswer
    {
        return (new QuestionHelper($this))->choice($question, $choices, $default, true, $validator, $sanitizer);
    }

    public function progress(int $steps = null): ProgressBar
    {
        return new ProgressBar($this, $steps);
    }

    public function progressStart(int $steps = null, ?int $redrawFreq = null, ?float $minSecsBetweenRedraws = 0.000025): ProgressBar
    {
        $progress = $this->progress($steps);
        $progress->setRedrawFrequency($redrawFreq);
        $progress->minSecondsBetweenRedraws($minSecsBetweenRedraws ?? 0);
        $progress->start();

        return $progress;
    }

    public function progressFinish(ProgressBar $progress): StyleInterface
    {
        $progress->finish();
        $this->newLine(2);

        return $this;
    }

    public function prependText(): StyleInterface
    {
        if ("\n" !== mb_substr($fetched = $this->buffer->fetch(), -1)) {
            $this->newline();
        }

        return $this;
    }

    public function prependBlock(): StyleInterface
    {
        $chars = mb_substr(str_replace(PHP_EOL, "\n", $this->buffer->fetch()), -2);
        $count = isset($chars[0]) ? 2 - mb_substr_count($chars, "\n") : 1;

        $this->newline($count);

        return $this;
    }

    public function getMaxLength(): int
    {
        return min(Terminal::x($this->maxLength) - (int) (\DIRECTORY_SEPARATOR === '\\'), $this->maxLength);
    }

    public function strLength(string $string): int
    {
        return Helper::width(Helper::removeDecoration($this->getFormatter(), $string));
    }

    public function pad(string $string, int $padding, string $character = ' ', int $orientation = STR_PAD_BOTH): string
    {
        $anchor = str_replace($string, str_repeat('x', $this->strLength($string)), $string);
        $padded = str_pad($anchor, $padding, $character, $orientation);

        return str_replace($anchor, $string, $padded);
    }

    public function padByTermWidth(string $string, string $character = ' ', int $orientation = STR_PAD_BOTH): string
    {
        return $this->pad($string, $this->getMaxLength(), $character, $orientation);
    }

    /**
     * @param string|iterable $lines
     *
     * @return string[]
     */
    private function reduceBuffer($lines): array
    {
        return array_map(function ($value) {
            return mb_substr($value, -4);
        }, array_merge((array) $this->buffer->fetch(), (array) $lines));
    }

    private function setupBuffer(OutputInterface $output): StyleInterface
    {
        $this->buffer = new BufferedOutput($output->getVerbosity(), false, clone $this->getFormatter());

        return $this;
    }
}
