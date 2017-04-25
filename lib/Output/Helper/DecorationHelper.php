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

class DecorationHelper
{
    /**
     * @var string|null
     */
    private $fg;

    /**
     * @var string|null
     */
    private $bg;

    /**
     * @var string[]
     */
    private $options;

    /**
     * @param string|null $fg
     * @param string|null $bg
     * @param string[]    ...$options
     */
    public function __construct(string $fg = null, string $bg = null, ...$options)
    {
        $this->fg = $fg;
        $this->bg = $bg;
        $this->options = $options;
    }

    /**
     * @param string|null $fg
     *
     * @return self
     */
    public function setForeground(string $fg = null): self
    {
        $this->fg = $fg;

        return $this;
    }

    /**
     * @param string|null $bg
     *
     * @return self
     */
    public function setBackground(string $bg = null): self
    {
        $this->bg = $bg;

        return $this;
    }

    /**
     * @param array ...$options
     *
     * @return self
     */
    public function setOptions(...$options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string|string[] $lines
     *
     * @return string|string[]
     */
    public function decorate($lines)
    {
        return is_array($lines) ? $this->markupLines($lines) : $this->markupString($lines);
    }

    /**
     * @param string[]    $lines
     *
     * @return string[]
     */
    private function markupLines(array $lines): array
    {
        return array_map(function ($l) {
            return $this->markupString($l);
        }, $lines);
    }

    /**
     * @param string|null $string
     *
     * @return string
     */
    private function markupString(string $string = null): string
    {
        if (null !== $markup = $this->markup($string)) {
            return $markup;
        }

        return (string) $string;
    }

    /**
     * @param string $line
     *
     * @return string
     */
    private function markup(string $line): ?string
    {
        $markup = $this->applyMarkup('options', implode(',', $this->options),
            $this->applyMarkup('bg', $this->bg, $this->applyMarkup('fg', $this->fg)));

        return '' === $markup ? null : sprintf('<%s>%s</>', $markup, $line);
    }

    /**
     * @param string      $type
     * @param string|null $value
     * @param string      $markup
     *
     * @return string
     */
    private function applyMarkup(string $type, string $value = null, string $markup = ''): string
    {
        if ($value) {
            if (strlen($markup) > 0) {
                $markup .= ';';
            }

            $markup .= sprintf('%s=%s', $type, $value);
        }

        return $markup;
    }
}

/* EOF */
