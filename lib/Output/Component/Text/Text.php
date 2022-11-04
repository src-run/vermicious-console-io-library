<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Text;

use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Output\Utility\Interpolate\PsrStringInterpolatorTrait;

class Text
{
    use PsrStringInterpolatorTrait;
    use StyleAwareInternalTrait;

    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
    }

    /**
     * @param string|string[] $lines
     * @param mixed[]         $replacements
     */
    public function text($lines, array $replacements = []): self
    {
        $this->style()->prependText();
        $this->style()->writeln(array_map(function (string $line) {
            return sprintf(' %s', $line);
        }, (array) self::interpolate($lines, $replacements)));

        return $this;
    }

    /**
     * @param string|string[] $lines
     * @param mixed[]         $replacements
     */
    public function comment($lines, array $replacements = []): self
    {
        $this->style()->prependText();
        $this->style()->writeln(array_map(function ($line) {
            return sprintf(' // %s', $line);
        }, (array) self::interpolate($lines, $replacements)));

        return $this;
    }

    /**
     * @param string|string[] $lines
     * @param mixed[]         $replacements
     */
    public function muted($lines, array $replacements = []): self
    {
        $this->style()->writeln((new Markup('black', null, 'bold'))->markupLines(
            (array) self::interpolate($lines, $replacements)
        ));

        return $this;
    }

    /**
     * @param int $length
     */
    public function separator(int $length = null, string $character = '-'): self
    {
        $this->muted(str_repeat($character, $length ?: $this->style()->getMaxLength()));

        return $this;
    }
}
