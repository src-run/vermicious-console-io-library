<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Helper\Text\BlockHelper;
use SR\Console\Output\Helper\Progress\ConcisePercentageProgressHelper;
use SR\Console\Output\Helper\Progress\ConciseProgressHelper;
use SR\Console\Output\Helper\Progress\DefaultProgressHelper;
use SR\Console\Output\Helper\Progress\VerbosePercentageProgressHelper;
use SR\Console\Output\Helper\Progress\VerboseProgressHelper;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $progress = $s->progressStart(5);
    $progress->advance(1);
    $progress->advance(1);
    $progress->advance(2);
    $progress->advance(1);
    $s->progressFinish($progress);

    /** @var \SR\Console\Output\Helper\Progress\AbstractProgressHelper[] $helpers */
    $helpers = [
        new DefaultProgressHelper($s),
        new ConciseProgressHelper($s),
        new VerboseProgressHelper($s)
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

    /** @var \SR\Console\Output\Helper\Progress\AbstractProgressHelper[] $helpers */
    $helpers = [
        new DefaultProgressHelper($s),
        new ConciseProgressHelper($s),
        new VerboseProgressHelper($s)
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

    /** @var \SR\Console\Output\Helper\Progress\AbstractPercentageProgressHelper[] $helpers */
    $helpers = [
        new ConcisePercentageProgressHelper($s),
        new VerbosePercentageProgressHelper($s)
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

    $h = new ConciseProgressHelper($s);
    $h->create(2, 'Doing Work');
    $h->step();
    $h->messages()->render()->render()->render();
    $h->messages()->progressHelper()->step();
    $h->messages()->render()->render()->render();
    $h->finish();
};
