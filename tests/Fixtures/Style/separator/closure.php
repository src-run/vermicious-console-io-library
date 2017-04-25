<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 120);

    $s->separator(1);
    $s->newline();

    $s->separator(10);
    $s->newline();

    $s->separator();
    $s->newline();
};
