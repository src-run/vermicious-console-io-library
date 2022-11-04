<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Component\Progress\ConciseProgress;
use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $h = new ConciseProgress($s);
    $h->create(2, 'Doing Work');
    $h->step();
    $h->messages()->render()->render()->render();
    $h->messages()->progressHelper()->step();
    $h->messages()->render()->render()->render();
    $h->finish();
};
