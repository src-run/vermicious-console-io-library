<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Helper\BlockHelper;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->table([
        'header 1',
        'header 2',
    ], [
        'row 1a',
        'row 2a',
    ], [
        'row 1b',
        'row 2b',
    ]);

    $s->tableVertical([
        'header 1',
        'header 2',
    ], [
        'row 1a',
        'row 1b',
    ], [
        'row 2a',
        'row 2b',
    ]);
};
