<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Helper\Lists\ListingHelper;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $list = [
        'item 1',
        'item 2',
        'item 3',
        'item 4',
        'item 5',
    ];

    $s->listing($list);

    $h = new ListingHelper($s);
    $h->listing($list);

    foreach ($list as $line) {
        $h->line($line);
    }

    $h->listingClose();

    foreach ($list as $line) {
        $h->line($line, true);
    }
};
