<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Component\Progress\AbstractPercentageProgress;
use SR\Console\Output\Component\Progress\ConcisePercentageProgress;
use SR\Console\Output\Component\Progress\VerbosePercentageProgress;
use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    /** @var AbstractPercentageProgress[] $helpers */
    $helpers = [
        new ConcisePercentageProgress($s),
        new VerbosePercentageProgress($s),
    ];

    foreach ($helpers as $h) {
        $h->setRedrawsFreq(1);
        $h->create(null, 'Doing Work');
        for ($i = 10; $i < 100; $i = $i + 10) {
            $h->messages()->action('working up to %s%% position...', $i);
            $h->percent($i);
            usleep(2500);
        }
        $h->messages()->action('all done!');
        $h->finish();
    }

    foreach ($helpers as $h) {
        $h->create(null, 'Doing Work');
        for ($i = 0; $i <= 100; ++$i) {
            $h->messages()->action('working up to %s%% position...', $i);
            $h->percent($i);
            usleep(2500);
        }
        $h->messages()->action('all done!');
        $h->finish();
    }
};
