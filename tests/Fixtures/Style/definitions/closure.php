<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 120);

    $s->definitions([
        'definition name 1'           => 'value 1',
        'definition name 2'           => 'value 2',
        'definition name 3'           => 'value 3',
        'definition name 4'           => 'value 4',
        'alternate definition name 5' => 'value 5',
    ]);
};
