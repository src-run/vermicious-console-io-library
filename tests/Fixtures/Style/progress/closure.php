<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Component\Progress\ConcisePercentageProgress;
use SR\Console\Output\Component\Progress\ConciseProgress;
use SR\Console\Output\Component\Progress\DefaultProgress;
use SR\Console\Output\Component\Progress\VerbosePercentageProgress;
use SR\Console\Output\Component\Progress\VerboseProgress;
use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $progress = $s->progressStart(5);
    $progress->advance(1);
    $progress->advance(1);
    $progress->advance(2);
    $progress->advance(1);
    $s->progressFinish($progress);

    /** @var \SR\Console\Output\Component\Progress\AbstractProgressHelper[] $helpers */
    $helpers = [
        new DefaultProgress($s),
        new ConciseProgress($s),
        new VerboseProgress($s),
    ];

    foreach ($helpers as $h) {
        $h->create(10, 'Doing Work');
        $h->messages()->action('step 1');
        $h->step();
        $h->messages()->action('step 2');
        $h->step();
        $h->messages()->action('step 3-4');
        $h->step(2);
        $h->messages()->action('step 5');
        $h->step();
        $h->messages()->action('step 6-9');
        $h->step(4);
        $h->messages()->action('step 10');
        $h->step();
        $h->messages()->action('all done!');
        $h->finish();
    }

    /** @var \SR\Console\Output\Component\Progress\AbstractProgressHelper[] $helpers */
    $helpers = [
        new DefaultProgress($s),
        new ConciseProgress($s),
        new VerboseProgress($s),
    ];

    foreach ($helpers as $h) {
        $h->setNewlinesAtCreate(1);
        $h->setNewlinesAtFinish(1);
        $h->create(2, 'Doing Work');
        $h->messages()->action('step 1');
        $h->step();
        $h->messages()->action('step 2');
        $h->step();
        $h->finish();
    }

    /** @var \SR\Console\Output\Component\Progress\AbstractPercentageProgress[] $helpers */
    $helpers = [
        new ConcisePercentageProgress($s),
        new VerbosePercentageProgress($s),
    ];

    foreach ($helpers as $h) {
        $h->create(null, 'Doing Work');
        for ($i = 10; $i < 100; $i = $i + 10) {
            $h->messages()->action('working up to %s%% position...', $i);
            $h->percent($i);
        }
        $h->messages()->action('all done!');
        $h->finish();
    }

    $h = new ConciseProgress($s);
    $h->create(2, 'Doing Work');
    $h->step();
    $h->messages()->render()->render()->render();
    $h->messages()->progressHelper()->step();
    $h->messages()->render()->render()->render();
    $h->finish();
};
