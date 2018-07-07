<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Progress;

use SR\Console\Output\Style\StyleInterface;

final class VerbosePercentageProgress extends AbstractPercentageProgress
{
    /**
     * @param StyleInterface $io
     */
    public function __construct(StyleInterface $io)
    {
        parent::__construct($io);

        $this->setFormatLines([
            '      Progress : [%bar%] (%percent:3s%%)',
            ' Time Estimate : %elapsed:6s% / %estimated:-6s%',
            ' %context:13s% : %action%',
        ]);
    }
}
