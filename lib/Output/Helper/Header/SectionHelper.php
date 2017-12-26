<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper\Header;

use SR\Console\Output\Helper\Style\DecorationHelper;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;

class SectionHelper
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
     * @param string $section
     *
     * @return self
     */
    public function section(string $section): self
    {
        $this->style->prependBlock();
        $this->style->writeln([
            $this->compileSection($section, 'white', 'magenta'),
        ]);
        $this->style->newline();

        return $this;
    }

    /**
     * @param string $section
     *
     * @return self
     */
    public function subSection(string $section): self
    {
        $this->style->prependBlock();
        $this->style->writeln([
            $this->compileSection($section, 'white', 'blue'),
        ]);
        $this->style->newline();

        return $this;
    }

    /**
     * @param string      $section
     * @param int         $iteration
     * @param int         $size
     * @param string|null $type
     *
     * @return self
     */
    public function enumeratedSection(string $section, int $iteration, int $size = null, string $type = null): self
    {
        $this->style->prependBlock();
        $this->style->writeln([
            $this->compileEnumeratedString($iteration, $size, $type),
            sprintf(' # %s', $section),
        ]);
        $this->style->newline();

        return $this;
    }

    /**
     * @param int         $iteration
     * @param int|null    $size
     * @param string|null $type
     *
     * @return string
     */
    private function compileEnumeratedString(int $iteration, int $size = null, string $type = null): string
    {
        $header = sprintf(' # <em>[ %d ', $iteration);

        if (null !== $size) {
            $header = sprintf('%sof %d ', $header, $size);
        }

        return sprintf('%s]</em> %s', $header, $type ?: '');
    }

    /**
     * @param string      $section
     * @param string|null $fg
     * @param string|null $bg
     *
     * @return string
     */
    private function compileSection(string $section, string $fg = null, string $bg = null): string
    {
        return (new DecorationHelper($fg, $bg))->decorate(
            $this->style->padByTermWidth(sprintf('[ %s ]', strtoupper($section)))
        );
    }
}
