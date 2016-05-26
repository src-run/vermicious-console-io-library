<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Tests\Style\StyleWithForcedLineLength;

//Ensure formatting tables when using multiple headers with TableCell
return function (InputInterface $input, OutputInterface $output) {
    $output = new StyleWithForcedLineLength($input, $output);
    $output->progressStart(10);
    for($i = 0; $i < 10; $i++) {
        $output->progressAdvance(1);
    }
    $output->progressFinish();
};
