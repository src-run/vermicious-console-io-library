<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Listing;

use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;

final class SimpleList
{
    use StyleAwareInternalTrait;

    /**
     * @var \Closure
     */
    protected $lineFormatter;

    /**
     * @var bool
     */
    private $listingPrepended = false;

    /**
     * @var bool
     */
    private $listingAppended = false;

    public function __construct(StyleInterface $style, \Closure $lineFormatter = null)
    {
        $this->setStyle($style);
        $this->setLineFormatter($lineFormatter);
    }

    public function setLineFormatter(\Closure $lineFormatter = null): self
    {
        $this->lineFormatter = $lineFormatter ?? function ($line) {
            return sprintf(' * %s', $line);
        };

        return $this;
    }

    public function listingStart(): self
    {
        $this->style()->prependText();
        $this->listingPrepended = true;
        $this->listingAppended = false;

        return $this;
    }

    public function listingClose(): self
    {
        $this->style()->newline();
        $this->listingPrepended = false;
        $this->listingAppended = true;

        return $this;
    }

    public function line(string $line, bool $close = false): self
    {
        if (false === $this->listingPrepended) {
            $this->listingStart();
        }

        $this->writeListLines([$line]);

        if (true === $close && false === $this->listingAppended) {
            $this->listingClose();
        }

        return $this;
    }

    /**
     * @param string[] $listing
     */
    public function listing(array $listing): self
    {
        $this->listingStart();
        $this->writeListLines($listing);
        $this->listingClose();

        return $this;
    }

    /**
     * @param string[] $lines
     */
    private function writeListLines(array $lines): self
    {
        $this->style()->writeln(array_map($this->lineFormatter, $lines));

        return $this;
    }
}
