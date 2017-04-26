<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->comment('a single line comment string');
    $s->comment([
        'a multi-line comment string',
        'with a second line',
        'and a final, third line',
    ]);
};
