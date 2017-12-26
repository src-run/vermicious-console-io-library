<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper\Text;

use SR\Console\Output\Helper\Style\DecorationHelper;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;

class TextHelper
{
    use StyleAwareInternalTrait;

    /**
     * @param StyleInterface $style
     */
    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
    }

    /**
     * @param string|string[] $lines
     *
     * @return self
     */
    public function text($lines): self
    {
        $this->style->prependText();
        $this->style->writeln(array_map(function (string $line) {
            return sprintf(' %s', $line);
        }, (array) $lines));

        return $this;
    }

    /**
     * @param string|string[] $lines
     *
     * @return self
     */
    public function comment($lines): self
    {
        $this->style->prependText();
        $this->style->writeln(array_map(function ($line) {
            return sprintf(' // %s', $line);
        }, (array) $lines));

        return $this;
    }

    /**
     * @param string|string[] $lines
     *
     * @return self
     */
    public function muted($lines): self
    {
        $this->style->writeln(array_map(function ($line) {
            return (new DecorationHelper('black', null, 'bold'))->decorate($line);
        }, (array) $lines));

        return $this;
    }

    /**
     * @param int    $length
     * @param string $character
     *
     * @return self
     */
    public function separator(int $length = null, string $character = '-'): self
    {
        $this->muted(str_repeat($character, $length ?: $this->style->getMaxLength()));

        return $this;
    }
}
