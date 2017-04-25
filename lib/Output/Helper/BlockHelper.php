<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper;

use SR\Console\Output\Style\StyleAwareTrait;
use SR\Console\Output\Style\StyleInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Helper;

class BlockHelper
{
    use StyleAwareTrait;

    /**
     * @var int
     */
    const TYPE_SM = 1024;

    /**
     * @var int
     */
    const TYPE_MD = 2048;

    /**
     * @var int
     */
    const TYPE_LG = 4096;

    /**
     * @var int
     */
    private $type;

    /**
     * @param StyleInterface $style
     * @param int            $type
     */
    public function __construct(StyleInterface $style, int $type = self::TYPE_SM)
    {
        $this->setStyle($style);
        $this->setType($type);
    }

    /**
     * @param int $type
     *
     * @return self
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array       $lines
     * @param string|null $header
     * @param string|null $prefix
     * @param string|null $fg
     * @param string|null $bg
     * @param array       ...$options
     *
     * @return self
     */
    public function write(array $lines, string $header = null, string $prefix = null, string $fg = null, string $bg = null, ...$options): self
    {
        $lines = static::wordwrap($this->prependHeader($lines, $header), $prefix, $this->io->getMaxLength());
        $lines = $this->padLength($this->padHeight($this->prefix($lines, $prefix)));

        $decorator = new DecorationHelper($fg, $bg, ...$options);

        $this->io->prependBlock();
        $this->io->writeln($decorator->decorate($lines));
        $this->io->newline();

        return $this;
    }

    /**
     * @param array       $lines
     * @param string|null $prefix
     * @param int         $length
     *
     * @return array
     */
    static public function wordwrap(array $lines, string $prefix = null, int $length = 80): array
    {
        $wrapped = [];
        foreach ($lines as $l) {
            $wrapped = array_merge($wrapped, explode(PHP_EOL, wordwrap(
                OutputFormatter::escape($l), $length - Helper::strlen($prefix) - 3, PHP_EOL, true
            )));
        }

        return $wrapped;
    }

    /**
     * @param array $lines
     *
     * @return array
     */
    private function padHeight(array $lines): array
    {
        if (self::TYPE_SM === $this->type) {
            return $lines;
        }

        return array_merge([''], $lines, ['']);
    }

    /**
     * @param array       $lines
     * @param string|null $header
     *
     * @return array
     */
    private function prependHeader(array $lines, string $header = null): array
    {
        if (null !== $header) {
            switch($this->type) {
                case self::TYPE_LG;
                    $header = sprintf('[ %s ]', strtoupper($header));
                    array_unshift($lines, str_repeat('-', $this->io->strLength($header)));
                    array_unshift($lines, $header);
                    break;

                case self::TYPE_MD;
                    $lines[0] = sprintf('[ %s ] %s', $header, $lines[0]);
                    break;

                case self::TYPE_SM;
                default:
                    $lines[0] = sprintf('[%s] %s', $header, $lines[0]);
                    break;
            }
        }

        return $lines;
    }

    /**
     * @param array $lines
     *
     * @return array
     */
    private function padLength(array $lines): array
    {
        return array_map(function ($line) {
            return $this->io->padByTermWidth(sprintf('%s%s', ' ', $line), ' ', STR_PAD_RIGHT);
        }, $lines);
    }

    /**
     * @param array       $lines
     * @param string|null $prefix
     *
     * @return array
     */
    private function prefix(array $lines, string $prefix = null)
    {
        if (null !== $prefix) {
            $lines = array_map(function ($line) use ($prefix) {
                return sprintf('%s %s', $prefix, $line);
            }, $lines);
        }

        return $lines;
    }
}

/* EOF */
