<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Component\Progress\AbstractProgressHelper;
use SR\Console\Output\Component\Progress\ConciseProgress;
use SR\Console\Output\Component\Progress\DefaultProgress;
use SR\Console\Output\Component\Progress\VerboseProgress;
use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    /** @var AbstractProgressHelper[] $helpers */
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
};
