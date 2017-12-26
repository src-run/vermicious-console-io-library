<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Helper\Text\BlockHelper;
use SR\Console\Output\Helper\Table\TableHorizontalHelper;
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

    $table = new TableHorizontalHelper($s);
    $table->writeRows([
        'row 1a',
        'row 1b',
        'row 1c',
    ], [
        'row 2a',
        'row 2b',
        'row 2c',
    ], [
        'row 3a',
        'row 3b',
        'row 3c',
    ]);
};
