<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;
use SR\Console\Tests\Fixtures\ApplicationWithProps;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->applicationTitle(new ApplicationWithProps());
    $s->write('Starting up...');
    $s->writeln('done.');
    $s->newline(6);

    $s->section('Text helpers');

    $s->text('A line of text');
    $s->separator();
    $s->comment('A comment');
    $s->separator();
    $s->muted('Some muted text');
    $s->separator();
    $s->listing(['a', 'listing']);
    $s->separator();
    $s->definitions(['definition-name' => 'definition-value']);

    $s->section('Title helpers');

    $s->title('A title');
    $s->separator();
    $s->section('Section');
    $s->separator();
    $s->subSection('Sub section');
    $s->separator();
    $s->enumeratedSection('Section', 4, 102, 'type');

    $s->section('Block helpers');
    $s->block('Block');
    $s->separator();
    $s->info('Info block');
    $s->separator();
    $s->success('Success block');
    $s->separator();
    $s->warning('Warning block');
    $s->separator();
    $s->error('Error block');
    $s->separator();
    $s->critical('Critical block');

    $s->section('Action Helpers');
    $s->action('Action');
    $s->actionDone();
    $s->separator();
    $s->action('Action');
    $s->actionFail();
    $s->separator();
    $s->action('Action');
    $s->actionStop();
    $s->separator();
    $s->action('Action');
    $s->actionOkay();

    $s->section('Table helpers');
    $s->table([
        'header',
    ], [
        'row',
    ]);
    $s->separator();
    $s->tableVertical([
        'header',
    ], [
        'row',
    ]);
};
