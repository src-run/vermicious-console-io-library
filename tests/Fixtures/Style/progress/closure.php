<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Helper\BlockHelper;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 120);

    $progress = $s->progressStart(5);
    $progress->advance(1);
    $progress->advance(1);
    $progress->advance(2);
    $progress->advance(1);
    $s->progressFinish($progress);
};
