<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Tests\Style\StyleWithForcedLineLength;

//Ensure has single blank line between blocks
return function (InputInterface $input, OutputInterface $output) {
    $output = new StyleWithForcedLineLength($input, $output);
    $output->warning('Warning');
    $output->caution('Caution');
    $output->error('Error');
    $output->success('Success');
    $output->smallSuccess('Title', 'Success');
    $output->note('Note');
    $output->block('Custom block', 'CUSTOM', 'fg=white;bg=green', 'X ', true);
    $output->applicationTitle('Name', '0.1.0', null, ['Author', 'First Last'], ['License', 'MIT']);
    $output->applicationTitle('Name', '0.1.0', 'abcdefg', ['Author', 'First Last'], ['License', 'MIT']);
    $output->section('Section');
    $output->subSection('Sub-section');
    $output->numberedSection(1, 10, 'PRE', 'Message');
};
