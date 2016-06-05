<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Tests\Style\StyleWithForcedLineLength;

//Ensure formatting tables when using multiple headers with TableCell
return function (InputInterface $input, OutputInterface $output) {
    $headers = [
        ['ISBN', 'Title', 'Author'],
    ];

    $rows = [
        [
            '978-0521567817',
            'De Monarchia',
            new \Symfony\Component\Console\Helper\TableCell("Dante Alighieri\nspans multiple rows", array('rowspan' => 2)),
        ],
        ['978-0804169127', 'Divine Comedy'],
    ];

    $output = new StyleWithForcedLineLength($input, $output);
    $output->table($rows, $headers);

    $output->table($rows);
};
