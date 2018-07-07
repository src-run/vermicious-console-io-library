<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s
        ->action('default action with "custom" result')
        ->result('custom', new Markup(Markup::C_WHITE, Markup::C_MAGENTA, Markup::O_BOLD, Markup::O_REVERSE));

    $s
        ->action('default action with "warn" result')
        ->resultWarn();

    $s
        ->action('default action with "done" result')
        ->resultDone();

    $s
        ->action('default action with "okay" result')
        ->resultOkay();

    $s
        ->action('default action with "stop" result')
        ->resultStop();

    $s
        ->action('default action with "fail" result')
        ->resultFail();

    $s
        ->action()
        ->setSupportExtras(true)
        ->setNewlinesCount(4)
        ->action('custom action with extras and 4 newlines')
        ->result('result')
        ->extras('an extra');

    $s
        ->action()
        ->setNewlinesCount(10)
        ->setSupportExtras(true)
        ->action('custom action with extras and 10 newlines and "foo" result')
        ->result('foo')
        ->extras('an extra', 'a second extras');

    $s
        ->action('custom action with no result and extras')
        ->setSupportExtras(true)
        ->extras('one', 'two', 'three');

    $s
        ->action('custom action with no result and no extras and early complete')
        ->setNewlinesCount(3)
        ->complete();

    $s
        ->action('bracketed action with "complete" result', 'bracketed')
        ->resultOkay('complete');

    $s
        ->action('bracketed action with no newlines and "error" result', 'bracketed')
        ->setNewlinesCount(0)
        ->resultFail('error');

    $s
        ->action('bracketed action with 5 newlines and extras and "warning" result', 'bracketed')
        ->setNewlinesCount(5)
        ->setSupportExtras(true)
        ->resultFail('warning')
        ->extras('one', 'two', 'three');

    $s
        ->action('bracketed action with "foobar" result', 'bracketed')
        ->result('foobar');
};
