<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Block;

use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Output\Utility\Interpolate\PsrStringInterpolatorTrait;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Helper;

class Block
{
    use PsrStringInterpolatorTrait;
    use StyleAwareInternalTrait;

    /**
     * @var int
     */
    public const TYPE_SM = 1024;

    /**
     * @var int
     */
    public const TYPE_MD = 2048;

    /**
     * @var int
     */
    public const TYPE_LG = 4096;

    /**
     * @var int
     */
    private $type;

    public function __construct(StyleInterface $style, int $type = self::TYPE_MD)
    {
        $this->setStyle($style);
        $this->setType($type);
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param mixed[] $replacements
     * @param Markup  $markup
     */
    public function write(array $text, string $head = null, array $replacements = [], string $prefix = null, Markup $markup = null): self
    {
        $markup = $markup ?: new Markup();

        $head = null !== $head ? self::interpolate($head, $replacements) : $head;
        $text = self::interpolate($text, $replacements);
        $text = static::wordwrap($this->prependHeader($text, $head), $prefix, $this->style()->getMaxLength());
        $text = $this->padLength($this->padHeight($this->prefix($text, $prefix)));

        $this->style()->prependBlock();
        $this->style()->writeln($markup->markupLines($text));
        $this->style()->newline();

        return $this;
    }

    public static function wordwrap(array $lines, string $prefix = null, int $length = 80): array
    {
        $wrapped = [];
        foreach ($lines as $l) {
            $wrapped = array_merge($wrapped, explode(PHP_EOL, wordwrap(
                OutputFormatter::escape($l),
                $length - Helper::width($prefix) - 3,
                PHP_EOL,
                true
            )));
        }

        return $wrapped;
    }

    private function padHeight(array $lines): array
    {
        if (self::TYPE_SM === $this->type) {
            return $lines;
        }

        return array_merge([''], $lines, ['']);
    }

    private function prependHeader(array $lines, string $header = null): array
    {
        if (null !== $header) {
            switch ($this->type) {
                case self::TYPE_LG:
                    $header = sprintf('[ %s ]', mb_strtoupper($header));
                    array_unshift($lines, str_repeat('-', $this->style()->strLength($header)));
                    array_unshift($lines, $header);
                    break;

                case self::TYPE_MD:
                    $lines[0] = sprintf('[ %s ] %s', $header, $lines[0]);
                    break;

                case self::TYPE_SM:
                default:
                    $lines[0] = sprintf('[%s] %s', $header, $lines[0]);
                    break;
            }
        }

        return $lines;
    }

    private function padLength(array $lines): array
    {
        return array_map(function ($line) {
            return $this->style()->padByTermWidth(sprintf('%s%s', ' ', $line), ' ', STR_PAD_RIGHT);
        }, $lines);
    }

    /**
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
