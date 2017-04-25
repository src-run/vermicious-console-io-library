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

class TextHelper
{
    use StyleAwareTrait;

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
        $this->io->prependText();
        $this->io->writeln(array_map(function (string $line) {
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
        $this->io->prependText();
        $this->io->writeln(array_map(function ($line) {
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
        $this->io->writeln(array_map(function ($line) {
            return (new DecorationHelper('black', null, 'bold'))->decorate($line);
        }, (array) $lines));

        return $this;
    }

    /**
     * @param array $listing
     *
     * @return self
     */
    public function listing(array $listing): self
    {
        $this->io->prependText();
        $this->io->writeln(array_map(function ($element) {
            return sprintf(' * %s', $element);
        }, $listing));
        $this->io->newline();

        return $this;
    }

    /**
     * @param array $definitions
     *
     * @return self
     */
    public function definitions(array $definitions): self
    {
        $length = max(array_map(function ($dt) {
            return strlen($dt);
        }, array_keys($definitions)));

        array_walk($definitions, function (&$dd, $dt) use ($length) {
            $dd = vsprintf(' %s -> %s', [
                (new DecorationHelper('black', null, 'bold'))->decorate($this->io->pad($dt, $length, ' ', STR_PAD_RIGHT)),
                (new DecorationHelper('white', null, 'bold'))->decorate($dd),
            ]);
        });

        $this->io->prependText();
        $this->io->writeln($definitions);
        $this->io->newline();

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
        $this->muted(str_repeat($character, $length ?: $this->io->getMaxLength()));

        return $this;
    }
}

/* EOF */
