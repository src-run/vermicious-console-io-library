<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Header;

use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;

class SectionHeader
{
    use StyleAwareInternalTrait;

    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
    }

    public function section(string $section): self
    {
        $this->style()->prependBlock();
        $this->style()->writeln([
            $this->compileSection($section, 'white', 'magenta'),
        ]);
        $this->style()->newline();

        return $this;
    }

    public function subSection(string $section): self
    {
        $this->style()->prependBlock();
        $this->style()->writeln([
            $this->compileSection($section, 'white', 'blue'),
        ]);
        $this->style()->newline();

        return $this;
    }

    /**
     * @param int $size
     */
    public function enumeratedSection(string $section, int $iteration, int $size = null, string $type = null): self
    {
        $this->style()->prependBlock();
        $this->style()->writeln([
            $this->compileEnumeratedString($iteration, $size, $type),
            sprintf(' # %s', $section),
        ]);
        $this->style()->newline();

        return $this;
    }

    private function compileEnumeratedString(int $iteration, int $size = null, string $type = null): string
    {
        $header = sprintf(' # <em>[ %d ', $iteration);

        if (null !== $size) {
            $header = sprintf('%sof %d ', $header, $size);
        }

        return sprintf('%s]</em> %s', $header, $type ?: '');
    }

    private function compileSection(string $section, string $fg = null, string $bg = null): string
    {
        return (new Markup($fg, $bg))->markupValue(
            $this->style()->padByTermWidth(sprintf('[ %s ]', mb_strtoupper($section)))
        );
    }
}
