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

use SR\Console\Input\Helper\QuestionHelper;
use SR\Console\Input\InputAwareTrait;
use SR\Console\Output\Helper\BlockHelper;
use SR\Console\Output\Helper\SectionHelper;
use SR\Console\Output\Helper\TableHorizontalHelper;
use SR\Console\Output\Helper\TableVerticalHelper;
use SR\Console\Output\Helper\TextHelper;
use SR\Console\Output\Helper\TitleHelper;
use SR\Console\Output\OutputAwareTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

class Style implements StyleInterface
{
    use InputAwareTrait;
    use OutputAwareTrait;

    /**
     * @var BufferedOutput
     */
    private $buffer;

    /**
     * @var int
     */
    private $verbosity;

    /**
     * @var int
     */
    private $maxLength;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param int             $maxLineLength
     */
    public function __construct(InputInterface $input, OutputInterface $output, int $maxLineLength = 160)
    {
        $this->maxLength = $maxLineLength;
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

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param int $verbosity
     *
     * @return StyleInterface
     */
    public function setActiveVerbosity(int $verbosity): StyleInterface
    {
        $this->verbosity = $verbosity;

        return $this;
    }

    /**
     * @param int $verbosity
     *
     * @return StyleInterface
     */
    public function setVerbosity($verbosity): StyleInterface
    {
        $this->output->setVerbosity($verbosity);

        return $this;
    }

    /**
     * @return int
     */
    public function getVerbosity(): int
    {
        return $this->output->getVerbosity();
    }

    /**
     * @return bool
     */
    public function isQuiet(): bool
    {
        return $this->output->isQuiet();
    }

    /**
     * @return bool
     */
    public function isVerbose(): bool
    {
        return $this->output->isVerbose();
    }

    /**
     * @return bool
     */
    public function isVeryVerbose(): bool
    {
        return $this->output->isVeryVerbose();
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->output->isDebug();
    }

    /**
     * @param bool $decorated
     *
     * @return StyleInterface
     */
    public function setDecorated($decorated): StyleInterface
    {
        $this->output->setDecorated($decorated);

        return $this;
    }

    /**
     * @return bool
     */
    public function isDecorated(): bool
    {
        return $this->output->isDecorated();
    }

    /**
     * @param OutputFormatterInterface $formatter
     *
     * @return StyleInterface
     */
    public function setFormatter(OutputFormatterInterface $formatter): StyleInterface
    {
        $this->output->setFormatter($formatter);

        return $this;
    }

    /**
     * @return OutputFormatterInterface
     */
    public function getFormatter(): OutputFormatterInterface
    {
        return $this->output->getFormatter();
    }

    /**
     * @param array[] $styles
     *
     * @return StyleInterface
     */
    public function addFormatterStyles(array $styles): StyleInterface
    {
        foreach ($styles as $name => $attributes) {
            $this->getFormatter()->setStyle($name, new OutputFormatterStyle(...$attributes));
        }

        return $this;
    }

    /**
     * @param int $verbosity
     *
     * @return StyleInterface
     */
    public function environment(int $verbosity = OutputInterface::VERBOSITY_NORMAL): StyleInterface
    {
        $environment = clone $this;
        $environment->setActiveVerbosity($verbosity);

        return $environment;
    }

    /**
     * @param string $lines
     * @param bool   $newLine
     * @param int    $options
     *
     * @return StyleInterface
     */
    public function write($lines, $newLine = false, $options = 0): StyleInterface
    {
        $this->output->write($lines, $newLine, $options | $this->verbosity);
        $this->buffer->write($this->reduceBuffer([$lines]), $newLine, $options | $this->verbosity);
        $this->writeLog($lines);

        return $this;
    }

    /**
     * @param array $lines
     * @param int   $options
     *
     * @return StyleInterface
     */
    public function writeln($lines, $options = 0): StyleInterface
    {
        $this->output->writeln($lines, $options | $this->verbosity);
        $this->buffer->writeln($this->reduceBuffer($lines), $options | $this->verbosity);
        $this->writeLog($lines);

        return $this;
    }

    /**
     * @param int $count
     *
     * @return StyleInterface
     */
    public function newline(int $count = 1): StyleInterface
    {
        $this->output->write($output = str_repeat(PHP_EOL, $count), false, $this->verbosity);
        $this->buffer->write($output, false, $this->verbosity);
        $this->writeLog($output);

        return $this;
    }

    private function writeLog($lines): self
    {
        $lines = array_map(function (string $line) {
            return Helper::removeDecoration($this->getFormatter(), $line);
        }, (array) $lines);
        $output = implode(PHP_EOL, $lines).PHP_EOL;
        file_put_contents('/tmp/style.out', $output, FILE_APPEND);

        return $this;
    }

    /**
     * @param string|string[] $lines
     *
     * @return StyleInterface
     */
    public function text($lines): StyleInterface
    {
        (new TextHelper($this))->text($lines);

        return $this;
    }

    /**
     * @param string|string[] $lines
     *
     * @return StyleInterface
     */
    public function comment($lines): StyleInterface
    {
        (new TextHelper($this))->comment($lines);

        return $this;
    }

    /**
     * @param string|string[] $lines
     *
     * @return StyleInterface
     */
    public function muted($lines): StyleInterface
    {
        (new TextHelper($this))->muted($lines);

        return $this;
    }

    /**
     * @param array $listing
     *
     * @return StyleInterface
     */
    public function listing(array $listing): StyleInterface
    {
        (new TextHelper($this))->listing($listing);

        return $this;
    }

    /**
     * @param array $definitions
     *
     * @return StyleInterface
     */
    public function definitions(array $definitions): StyleInterface
    {
        (new TextHelper($this))->definitions($definitions);

        return $this;
    }

    /**
     * @param int    $length
     * @param string $character
     *
     * @return StyleInterface
     */
    public function separator(int $length = null, string $character = '-'): StyleInterface
    {
        (new TextHelper($this))->separator($length, $character);

        return $this;
    }

    /**
     * @param string $title
     *
     * @return StyleInterface
     */
    public function title(string $title): StyleInterface
    {
        (new TitleHelper($this))->title($title);

        return $this;
    }

    /**
     * @param Application $application
     * @param array       ...$properties
     *
     * @return StyleInterface
     */
    public function applicationTitle(Application $application, ...$properties): StyleInterface
    {
        (new TitleHelper($this))->applicationTitle($application, ...$properties);

        return $this;
    }

    /**
     * @param string $section
     *
     * @return StyleInterface
     */
    public function section(string $section): StyleInterface
    {
        (new SectionHelper($this))->section($section);

        return $this;
    }

    /**
     * @param string $section
     *
     * @return StyleInterface
     */
    public function subSection(string $section): StyleInterface
    {
        (new SectionHelper($this))->subSection($section);

        return $this;
    }

    /**
     * @param string      $section
     * @param int         $iteration
     * @param int         $size
     * @param string|null $type
     *
     * @return StyleInterface
     */
    public function enumeratedSection(string $section, int $iteration, int $size = null, string $type = null): StyleInterface
    {
        (new SectionHelper($this))->enumeratedSection($section, $iteration, $size, $type);

        return $this;
    }

    /**
     * @param string|string[] $lines
     * @param string|null     $header
     * @param int             $type
     * @param string|null     $prefix
     * @param string|null     $fg
     * @param string|null     $bg
     * @param array           ...$options
     *
     * @return StyleInterface
     */
    public function block($lines, string $header = null, int $type = BlockHelper::TYPE_SM, string $prefix = null, string $fg = null, string $bg = null, ...$options): StyleInterface
    {
        (new BlockHelper($this, $type))->write((array) $lines, $header, $prefix, $fg, $bg, ...$options);

        return $this;
    }

    /**
     * @param string|array $lines
     * @param int          $type
     * @param string       $header
     *
     * @return StyleInterface
     */
    public function info($lines, int $type = BlockHelper::TYPE_SM, string $header = 'INFO'): StyleInterface
    {
        return $this->block((array) $lines, $header, $type, '--', 'white', 'blue');
    }

    /**
     * @param string|array $lines
     * @param int          $type
     * @param string       $header
     *
     * @return StyleInterface
     */
    public function success($lines, int $type = BlockHelper::TYPE_SM, string $header = 'OK'): StyleInterface
    {
        return $this->block((array) $lines, $header, $type, '||', 'black', 'green');
    }

    /**
     * @param string|array $lines
     * @param int          $type
     * @param string       $header
     *
     * @return StyleInterface
     */
    public function warning($lines, int $type = BlockHelper::TYPE_SM, string $header = 'WARN'): StyleInterface
    {
        return $this->block((array) $lines, $header, $type, '##', 'black', 'yellow');
    }

    /**
     * @param string|array $lines
     * @param int          $type
     * @param string       $header
     *
     * @return StyleInterface
     */
    public function error($lines, int $type = BlockHelper::TYPE_LG, string $header = 'ERR'): StyleInterface
    {
        return $this->block((array) $lines, $header, $type,'!!', 'white', 'red');
    }

    /**
     * @param string|array $lines
     * @param int          $type
     * @param string       $header
     *
     * @return StyleInterface
     */
    public function critical($lines, int $type = BlockHelper::TYPE_LG, string $header = 'CRITICAL'): StyleInterface
    {
        return $this->block((array) $lines, $header, $type,'**', 'white', 'red');
    }

    /**
     * @param array $headers
     * @param array $rows
     *
     * @return StyleInterface
     */
    public function table(array $headers, ...$rows): StyleInterface
    {
        (new TableHorizontalHelper($this))->write($headers, ...$rows);

        return $this;
    }

    /**
     * @param array $headers
     * @param array $rows
     *
     * @return StyleInterface
     */
    public function tableVertical(array $headers, ...$rows): StyleInterface
    {
        (new TableVerticalHelper($this))->write($headers, ...$rows);

        return $this;
    }

    /**
     * @param string        $question
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return string
     */
    public function ask(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): ?string
    {
        return (new QuestionHelper($this))->ask($question, $default, $validator, $sanitizer);
    }

    /**
     * @param string        $question
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $sanitizer
     *
     * @return string
     */
    public function askHidden(string $question, string $default = null, \Closure $validator = null, \Closure $sanitizer = null): ?string
    {
        return (new QuestionHelper($this))->askHidden($question, $default, $validator, $sanitizer);
    }

    /**
     * @param string $question
     * @param bool   $default
     *
     * @return bool
     */
    public function confirm(string $question, bool $default = true): bool
    {
        return (new QuestionHelper($this))->confirm($question, $default);
    }

    /**
     * @param string      $question
     * @param array       $choices
     * @param string|null $default
     *
     * @return string
     */
    public function choice(string $question, array $choices, string $default = null): ?string
    {
        return (new QuestionHelper($this))->choice($question, $choices, $default);
    }

    /**
     * @param int|null $steps
     *
     * @return ProgressBar
     */
    public function progressStart(int $steps = null): ProgressBar
    {
        $progress = new ProgressBar($this, $steps);
        $progress->start();

        return $progress;
    }

    /**
     * @param ProgressBar $progress
     *
     * @return StyleInterface
     */
    public function progressFinish(ProgressBar $progress): StyleInterface
    {
        $progress->finish();
        $this->newLine(2);

        return $this;
    }

    /**
     * @return StyleInterface
     */
    public function prependText(): StyleInterface
    {
        if ("\n" !== substr($fetched = $this->buffer->fetch(), -1)) {
            $this->newline();
        }

        return $this;
    }

    /**
     * @return StyleInterface
     */
    public function prependBlock(): StyleInterface
    {
        $chars = substr(str_replace(PHP_EOL, "\n", $this->buffer->fetch()), -2);
        $count = isset($chars[0]) ? 2 - substr_count($chars, "\n") : 1;

        $this->newline($count);

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxLength(): int
    {
        return min($this->termWidth() - (int) (DIRECTORY_SEPARATOR === '\\'), $this->maxLength);
    }

    /**
     * @param string $string
     *
     * @return int
     */
    public function strLength(string $string): int
    {
        return Helper::strlenWithoutDecoration($this->getFormatter(), $string);
    }

    /**
     * @return int
     */
    public function termHeight(): ?int
    {
        return (new Terminal())->getHeight();
    }

    /**
     * @return int
     */
    public function termWidth(): int
    {
        return (new Terminal())->getWidth() ?: $this->maxLength;
    }

    /**
     * @param string $string
     * @param int    $padding
     * @param string $character
     * @param int    $orientation
     *
     * @return string
     */
    public function pad(string $string, int $padding, string $character = ' ', int $orientation = STR_PAD_BOTH): string
    {
        $anchor = str_replace($string, str_repeat('x', $this->strLength($string)), $string);
        $padded = str_pad($anchor, $padding, $character, $orientation);

        return str_replace($anchor, $string, $padded);
    }

    /**
     * @param string $string
     * @param string $character
     * @param int    $orientation
     *
     * @return string
     */
    public function padByTermWidth(string $string, string $character = ' ', int $orientation = STR_PAD_BOTH): string
    {
        return $this->pad($string, $this->getMaxLength(), $character, $orientation);
    }

    /**
     * @param string[] $lines
     *
     * @return string[]
     */
    private function reduceBuffer($lines): array
    {
        return array_map(function ($value) {
            return substr($value, -4);
        }, array_merge((array) $this->buffer->fetch(), (array) $lines));
    }

    /**
     * @param OutputInterface $output
     *
     * @return StyleInterface
     */
    private function setupBuffer(OutputInterface $output): StyleInterface
    {
        $this->buffer = new BufferedOutput($output->getVerbosity(), false, clone $this->getFormatter());

        return $this;
    }
}

/* EOF */
