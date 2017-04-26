<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->text('a single line text string');
    $s->text([
        'a multi-line text string',
        'with a second line',
        'and a final, third line',
    ]);

};
