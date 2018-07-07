<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Style\Style;
use SR\Console\Tests\Fixtures\ApplicationWithProps;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
    $s->block([
        'Line 1 with {scalar} scalar replacement',
        'And line 2 with {integer} integer replacement',
        'And line 3 with {complex} complex replacement',
    ], 'Has "{scalar}" scalar, "{integer}" integer, and complex replacement', [
        'scalar' => 'simple-string',
        'integer' => 100,
        'complex' => new stdClass(),
    ]);
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
    $s->action('Action 1')->resultDone();
    $s->separator();
    $s->action('Action 2')->resultFail();
    $s->separator();
    $s->action('Action 3')->resultStop();
    $s->separator();
    $s->action('Action 4')->resultWarn();
    $s->separator();
    $s->action('Action 5')->resultOkay();

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
