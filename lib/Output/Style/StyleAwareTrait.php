<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Style;

/**
 * Trait StyleAwareTrait.
 */
trait StyleAwareTrait
{
    /**
     * @var StyleInterface
     */
    protected $io;

    /**
     * @param StyleInterface $io
     */
    public function setStyle(StyleInterface $io)
    {
        $this->io = $io;
    }
}

/* EOF */
