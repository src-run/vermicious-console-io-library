<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Tests\Style\StyleWithForcedLineLength;

//Ensure has single blank line between two titles
return function (InputInterface $input, OutputInterface $output) {
    $output = new StyleWithForcedLineLength($input, $output);
    $output->title('First title');
    $output->title('Second title');
};
