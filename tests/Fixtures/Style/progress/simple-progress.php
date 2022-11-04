<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $progress = $s->progressStart(5);
    $progress->setRedrawFrequency(null);
    $progress->minSecondsBetweenRedraws(0.00001);
    $progress->advance(1);
    $progress->advance(1);
    $progress->advance(2);
    $progress->advance(1);
    $s->progressFinish($progress);
};
