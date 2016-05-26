<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Style;

use SR\Console\Input\InputAwareTrait;
use SR\Console\Output\OutputAwareTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * Class Style.
 *
 * @author Kevin Bond      <kevinbond@gmail.com>
 * @author Rob Frawley 2nd <rmf@src.run>
 */
class Style extends OutputStyle implements StyleInterface
{
    use InputAwareTrait;
    use OutputAwareTrait;

    /**
     * @var SymfonyQuestionHelper
     */
    private $question;

    /**
     * @var ProgressBar
     */
    private $progress;

    /**
     * @var int
     */
    private $lineLength;

    /**
     * @var int
     */
    private $lineLengthMax;

    /**
     * @var BufferedOutput
     */
    private $outputBuffered;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output, $lineLengthMax = 160)
    {
        $this->input = $input;
        $this->output = $output;
        $this->outputBuffered = new BufferedOutput($output->getVerbosity(), false, clone $output->getFormatter());
        $this->lineLengthMax = $lineLengthMax;
        $this->lineLength = $this->lineLength();

        parent::__construct($output);

        $this->getFormatter()->setStyle('highlight', new OutputFormatterStyle('magenta'));
        $this->getFormatter()->setStyle('em', new OutputFormatterStyle(null, null, ['bold']));
        $this->getFormatter()->setStyle('success', new OutputFormatterStyle('black', 'green'));
    }

    /**
     * @return int
     */
    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }

    /**
     * @return bool
     */
    public function isQuiet()
    {
        return $this->getVerbosity() === OutputInterface::VERBOSITY_QUIET;
    }

    /**
     * @return bool
     */
    public function isNormal()
    {
        return $this->getVerbosity() === OutputInterface::VERBOSITY_NORMAL;
    }

    /**
     * @return bool
     */
    public function isVerbose()
    {
        return $this->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
    }

    /**
     * @return bool
     */
    public function isVeryVerbose()
    {
        return $this->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->getVerbosity() === OutputInterface::VERBOSITY_DEBUG;
    }

    /**
     * Formats a message as a block of text.
     *
     * @param string|array $msgLines The message to write in the block
     * @param string|null  $type     The block type (added in [] on first line)
     * @param string|null  $style    The style to apply to the whole block
     * @param string       $prefix   The prefix for the block
     * @param bool         $padding  Whether to add vertical padding
     */
    public function block($msgLines, $type = null, $style = null, $prefix = ' ', $padding = false)
    {
        $this->autoPrependBlock();

        $msgLines = (array) $msgLines;
        $lines = [];

        if (null !== $type) {
            $msgLines[0] = sprintf('[%s] %s', $type, $msgLines[0]);
        }

        foreach ((array) $msgLines as $key => $m) {
            $m = OutputFormatter::escape($m);
            $lines = array_merge(
                $lines,
                explode(PHP_EOL, wordwrap($m, $this->lineLength - Helper::strlen($prefix), PHP_EOL, true))
            );

            if (count($msgLines) > 1 && $key < count($msgLines) - 1) {
                $lines[] = '';
            }
        }

        if ($padding && $this->isDecorated()) {
            array_unshift($lines, '');
            $lines[] = '';
        }

        foreach ($lines as &$line) {
            $line = sprintf('%s%s', $prefix, $line);
            $line .= str_repeat(' ', $this->lineLength - Helper::strlenWithoutDecoration($this->getFormatter(), $line));

            if ($style) {
                $line = sprintf('<%s>%s</>', $style, $line);
            }
        }

        $this->writeln($lines);
        $this->newLine();
    }

    /**
     * @param string $separator
     *
     * @return string
     */
    public function getSeparatorFullWidth($separator = '-')
    {
        return sprintf('<fg=black;options=bold>%s</>', str_repeat($separator, $this->lineLength));
    }

    /**
     * @param string $name
     * @param null|string|int $version
     * @param mixed ...$more
     */
    public function applicationTitle($name, $version = null, ...$more)
    {
        $msgLines = [
            $this->getSeparatorFullWidth(),
            '<fg=black;options=bold>-</>',
            sprintf('<fg=black;options=bold>-</> <em>%s (v%s)</em> ', $name, (string)$version ?: ' master')
        ];

        $msgLines = array_merge($msgLines, $this->applicationTitleMore($more));

        $msgLines[] = '<fg=black;options=bold>-</>';
        $msgLines[] = $this->getSeparatorFullWidth();

        $this->autoPrependBlock();
        $this->writeln($msgLines);
        $this->newLine();
    }

    /**
     * @param array $more
     *
     * @return string[]
     */
    private function applicationTitleMore(array $more)
    {
        $msgLines = [];
        $length = 0;

        foreach ($more as $m) {
            if (strlen($m[0]) > $length) {
                $length = strlen($m[0]);
            }
        }

        $more = array_map(
            function ($m) use ($length) {
                $m[0] = sprintf('<fg=white>@%s</>', str_pad(strtolower($m[0]), $length, ' ', STR_PAD_RIGHT));
                $m[1] = sprintf('<fg=white>%s</>', $m[1]);

                return $m;
            },
            $more
        );

        if (count($more) > 0) {
            $msgLines[] = '<fg=black;options=bold>-</>';
        }

        foreach ($more as $m) {
            $msgLines[] = sprintf('<fg=black;options=bold>-</> %s %s ', ...$m);
        }

        return $msgLines;
    }

    /**
     * @param string $message
     */
    public function title($message)
    {
        $this->autoPrependBlock();
        $this->writeln(
            array(
                sprintf('<comment>%s</>', $message),
                sprintf(
                    '<comment>%s</>',
                    str_repeat('=', Helper::strlenWithoutDecoration($this->getFormatter(), $message))
                ),
            )
        );

        $this->newLine();
    }

    /**
     * @param string $message
     */
    public function section($message)
    {
        $padLength = $this->lineLength - Helper::strlenWithoutDecoration($this->getFormatter(), $message) - 4;

        $this->autoPrependBlock();
        $this->writeln(
            [
                sprintf(
                    '<bg=magenta;fg=white> [<bg=magenta;fg=white;options=bold>%s</><bg=magenta;fg=white>]%s </>',
                    $message,
                    str_repeat(' ', $padLength)
                ),
            ]
        );

        $this->newLine();
    }

    /**
     * @param string $message
     */
    public function subSection($message)
    {
        $padLength = $this->lineLength - Helper::strlenWithoutDecoration($this->getFormatter(), $message) - 6;
        $padLeft = round($padLength / 2);
        $padRight = $padLength - $padLeft;

        $this->autoPrependBlock();
        $this->writeln(
            [
                sprintf(
                    '<bg=yellow;fg=black> %s[ %s ]%s </>',
                    str_repeat(' ', $padLeft),
                    $message,
                    str_repeat(' ', $padRight)
                ),
            ]
        );

        $this->newLine();
    }

    /**
     * @param int    $i
     * @param int    $count
     * @param string $pre
     * @param string $message
     */
    public function numberedSection($i, $count, $pre, $message)
    {
        $this->autoPrependBlock();
        $this->writeln(
            [
                sprintf(' # <em>[ %d of %d ]</em> %s', $i, $count, strtoupper($pre)),
                sprintf(' # %s', $message),
            ]
        );

        $this->newLine();
    }

    /**
     * @param string[] $list
     */
    public function listing(array $list)
    {
        $list = array_map(function ($element) {
            return sprintf(' * %s', $element);
        }, $list);

        $this->autoPrependText();
        $this->writeln($list);
        $this->newLine();
    }

    /**
     * @param string|string[] $msgLines
     */
    public function text($msgLines)
    {
        $lines = array_map(function ($l) {
            return sprintf(' %s', $l);
        }, (array) $msgLines);

        $this->autoPrependText();
        $this->writeln($lines);
    }

    /**
     * @param string|string[] $msgLines
     * @param bool            $newLine
     */
    public function comment($msgLines)
    {
        $lines = array_map(
            function ($l) {
                return sprintf(' // %s', $l);
            },
            (array) $msgLines
        );

        $this->autoPrependText();
        $this->writeln($lines);
    }

    /**
     * @param string $title
     * @param string $message
     */
    public function smallSuccess($title, $message)
    {
        $this->block($message, $title, 'bg=green;fg=black', ' ', false);
    }

    /**
     * @param string $message
     */
    public function success($message)
    {
        $this->block($message, 'OK', 'fg=black;bg=green', ' ', true);
    }

    /**
     * @param string $message
     */
    public function error($message)
    {
        $this->block($message, 'ERROR', 'fg=white;bg=red', ' ', true);
    }

    /**
     * @param string $message
     */
    public function warning($message)
    {
        $this->block($message, 'WARNING', 'fg=white;bg=red', ' ', true);
    }

    /**
     * @param string $message
     */
    public function note($message)
    {
        $this->block($message, 'NOTE', 'fg=yellow', ' ! ');
    }

    /**
     * @param string $message
     */
    public function caution($message)
    {
        $this->block($message, 'CAUTION', 'fg=white;bg=red', ' ! ', true);
    }

    /**
     * @param string[] $rows
     * @param string[] $headers
     */
    public function table(array $rows, array $headers = null)
    {
        $rows = array_map(
            function ($value) {
                if (!is_array($value)) {
                    return $value;
                }

                $header = array_shift($value);
                array_unshift($value, sprintf('<fg=blue>%s</>', $header));

                return $value;
            },
            $rows
        );

        $style = new TableStyle();
        $style->setVerticalBorderChar('<fg=blue>|</>');
        $style->setHorizontalBorderChar('<fg=blue>-</>');
        $style->setCrossingChar('<fg=blue>+</>');
        $style->setCellHeaderFormat('%s');

        $table = new Table($this);
        $table->setStyle($style);

        if ($headers) {
            $table->setHeaders($headers);
        }
        $table->setRows($rows);
        $table->render();

        $this->newLine();
    }

    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null, $validator = null, $sanitizer = null)
    {
        $question = new Question($question, $default);
        $question->setValidator($validator);

        $return = $this->askQuestion($question);

        if ($sanitizer instanceof \Closure) {
            return $sanitizer($return);
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function askHidden($question, $validator = null)
    {
        $question = new Question($question);
        $question->setHidden(true);
        $question->setValidator($validator);

        return $this->askQuestion($question);
    }

    /**
     * {@inheritdoc}
     */
    public function confirm($question, $default = true)
    {
        return $this->askQuestion(new ConfirmationQuestion($question, $default));
    }

    /**
     * {@inheritdoc}
     */
    public function choice($question, array $choices, $default = null)
    {
        if (null !== $default) {
            $default = array_flip($choices)[$default];
        }

        return $this->askQuestion(new ChoiceQuestion($question, $choices, $default));
    }

    /**
     * {@inheritdoc}
     */
    public function progressStart($max = 0)
    {
        $this->progress = $this->createProgressBar($max);
        $this->progress->start();
    }

    /**
     * {@inheritdoc}
     */
    public function progressAdvance($step = 1)
    {
        $this->progress->advance($step);
    }

    /**
     * {@inheritdoc}
     */
    public function progressFinish()
    {
        $this->progress->finish();
        $this->newLine(2);

        $this->progress = null;
    }

    /**
     * {@inheritdoc}
     */
    public function createProgressBar($max = 0)
    {
        $progress = parent::createProgressBar($max);

        if ('\\' !== DIRECTORY_SEPARATOR) {
            $progress->setEmptyBarCharacter('░'); // light shade character \u2591
            $progress->setProgressCharacter('');
            $progress->setBarCharacter('▓'); // dark shade character \u2593
        }

        return $progress;
    }

    /**
     * @param Question $question
     *
     * @return string
     */
    public function askQuestion(Question $question)
    {
        if ($this->input->isInteractive()) {
            $this->autoPrependBlock();
        }

        if (!$this->question) {
            $this->question = new SymfonyQuestionHelper();
        }

        $answer = $this->question->ask($this->input, $this, $question);

        if ($this->input->isInteractive()) {
            $this->newLine();
            $this->outputBuffered->write("\n");
        }

        return $answer;
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        parent::writeln($messages, $type);

        $this->outputBuffered->writeln($this->reduceBuffer($messages), $type);
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        parent::write($messages, $newline, $type);

        $this->outputBuffered->write($this->reduceBuffer($messages), $newline, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function newLine($count = 1)
    {
        parent::newLine($count);

        $this->outputBuffered->write(str_repeat("\n", $count));
    }

    /**
     * @return int
     */
    private function getTerminalWidth()
    {
        $dimensions = (new Application())->getTerminalDimensions();

        return $dimensions[0] ?: $this->lineLengthMax;
    }

    /**
     * @return $this
     */
    private function autoPrependBlock()
    {
        $chars = substr(str_replace(PHP_EOL, "\n", $this->outputBuffered->fetch()), -2);

        if (!isset($chars[0])) {
            return $this->newLine();
        }

        $this->newLine(2 - substr_count($chars, "\n"));

        return $this;
    }

    /**
     * @return $this
     */
    private function autoPrependText()
    {
        $fetched = $this->outputBuffered->fetch();

        if ("\n" !== substr($fetched, -1)) {
            $this->newLine();
        }

        return $this;
    }

    /**
     * @param string[] $lines
     *
     * @return string[]
     */
    private function reduceBuffer($lines)
    {
        return array_map(
            function ($value) {
                return substr($value, -4);
            },
            array_merge((array) $this->outputBuffered->fetch(), (array) $lines)
        );
    }

    /**
     * @return int
     */
    private function lineLength()
    {
        return min($this->getTerminalWidth() - (int) (DIRECTORY_SEPARATOR === '\\'), $this->lineLengthMax);
    }
}

/* EOF */
