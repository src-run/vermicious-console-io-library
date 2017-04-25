<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 120);

    $s->section('A section string');

    $s->subSection('A sub section string');

    foreach (range(1, 9) as $i) {
        $s->enumeratedSection('An enumerated section string', $i, 9, sprintf('file-0%d.ext', $i));
    }
};
