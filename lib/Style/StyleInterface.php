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

use SR\Console\Input\InputAwareInterface;
use SR\Console\Output\OutputAwareInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface as BaseStyleInterface;

/**
 * Interface StyleInterface.
 */
interface StyleInterface extends BaseStyleInterface, OutputInterface, InputAwareInterface, OutputAwareInterface
{
    /**
     * {@inheritdoc}
     */
    public function setVerbosity($level);

    /**
     * @return int
     */
    public function getVerbosity();

    /**
     * @return bool
     */
    public function isQuiet();

    /**
     * @return bool
     */
    public function isNormal();

    /**
     * @return bool
     */
    public function isVerbose();

    /**
     * @return bool
     */
    public function isVeryVerbose();

    /**
     * @return bool
     */
    public function isDebug();

    /**
     * {@inheritdoc}
     */
    public function setFormatter(OutputFormatterInterface $formatter);

    /**
     * {@inheritdoc}
     */
    public function getFormatter();

    /**
     * {@inheritdoc}
     */
    public function setDecorated($decorated);

    /**
     * {@inheritdoc}
     */
    public function isDecorated();

    /**
     * Formats a message as a block of text.
     *
     * @param string|array $messages The message to write in the block
     * @param string|null  $type     The block type (added in [] on first line)
     * @param string|null  $style    The style to apply to the whole block
     * @param string       $prefix   The prefix for the block
     * @param bool         $padding  Whether to add vertical padding
     */
    public function block($messages, $type = null, $style = null, $prefix = ' ', $padding = false);

    /**
     * @param string $separator
     *
     * @return string
     */
    public function getSeparatorFullWidth($separator = '▬');

    /**
     * @param string          $name
     * @param null|string|int $version
     * @param null|string     $commit
     * @param mixed           ...$more
     */
    public function applicationTitle($name, $version = null, $commit = null, ...$more);

    /**
     * @param string $message
     */
    public function subSection($message);

    /**
     * @param int    $i
     * @param int    $count
     * @param string $pre
     * @param string $message
     */
    public function numberedSection($i, $count, $pre, $message);

    /**
     * {@inheritdoc}
     */
    public function comment($message);

    /**
     * @param string $title
     * @param string $message
     */
    public function smallSuccess($title, $message);

    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL);

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL);

    /**
     * {@inheritdoc}
     */
    public function newLine($count = 1);

    /**
     * @param string        $question
     * @param null|string   $default
     * @param null          $validator
     * @param null|\Closure $sanitizer
     *
     * @return mixed
     */
    public function ask($question, $default = null, $validator = null, $sanitizer = null);
}

/* EOF */
