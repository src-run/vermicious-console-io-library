<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 120);

    $s->listing([
        'item 1',
        'item 2',
        'item 3',
        'item 4',
        'item 5',
    ]);
};
