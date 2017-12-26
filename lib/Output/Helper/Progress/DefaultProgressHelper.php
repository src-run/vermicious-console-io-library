<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper\Progress;

use SR\Console\Output\Style\StyleInterface;

final class DefaultProgressHelper extends AbstractProgressHelper
{
    /**
     * @param StyleInterface $io
     */
    public function __construct(StyleInterface $io)
    {
        parent::__construct($io);

        $this->setBarCharacter(null);
        $this->setEmptyBarCharacter(null);
        $this->setProgressCharacter(null);
        $this->setFormatLines([]);
    }
}
