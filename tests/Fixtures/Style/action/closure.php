<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use SR\Console\Output\Component\Action\BracketedAction;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s
        ->action('default action with "custom" result')
        ->result('custom', null, new Markup(Markup::C_WHITE, Markup::C_MAGENTA, Markup::O_BOLD, Markup::O_REVERSE));

    $s
        ->action('default action with "warn" result')
        ->resultWarn();

    $s
        ->action('default action with "warn" result', ' --> ')
        ->resultWarn();

    $s
        ->action('default action with "warn" result', ' --> ')
        ->setSupportExtras(true)
        ->statusText('1st status')
            ->text('2nd status')
            ->finish()
        ->resultWarn()
        ->extras()
            ->text('foo')
            ->text('bar')
            ->text('baz')
            ->finish()
        ->complete();

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
        ->extras('an extra')
            ->finish()
        ->complete();

    $s
        ->action()
        ->setNewlinesCount(10)
        ->setSupportExtras(true)
        ->action('custom action with extras and 10 newlines and "foo" result')
        ->result('foo')
        ->extras('an extra')
            ->text('a second extras')
            ->finish()
        ->complete();

    $s
        ->action('custom action with no result and extras')
        ->setSupportExtras(true)
        ->extras('one', 'two')
            ->text('three')
            ->finish()
        ->complete();

    $s
        ->action('custom action with no result and no extras and early complete')
        ->setNewlinesCount(2)
        ->complete();

    $s
        ->action('custom action with no result and no extras and early complete', null, 'simple')
        ->setNewlinesCount(3)
        ->resultOkay();

    $s
        ->action('bracketed action with "complete" result', null, 'bracketed')
        ->resultOkay('complete');

    $s
        ->action('bracketed action with no newlines and "error" result', null, 'bracketed')
        ->setNewlinesCount(0)
        ->resultFail('error');

    $s
        ->action('bracketed action with 5 newlines and extras and "warning" result', null, 'bracketed')
        ->setNewlinesCount(5)
        ->setSupportExtras(true)
        ->resultFail('warning')
        ->extras('one', 'two', 'three')
            ->finish()
        ->complete();

    $s
        ->action('bracketed action with "foobar" result', null, 'bracketed')
        ->result('foobar');

    $a = $s->action('bracketed action with "done" result', null, 'bracketed');
    $a->resultDone(null, true);
    $t = $a->extras()
            ->text('foo')
            ->text('bar');

    TestCase::assertFalse($t->isInactive());

    $t
        ->finish()
        ->complete();

    TestCase::assertTrue($t->isInactive());

    $s
        ->action('bracketed action with "stop" result', null, 'bracketed')
        ->resultStop(null, true)
        ->extras('foo')
            ->finish()
        ->complete();

    $s
        ->action('bracketed action with "foobar" result and prefix', null, 'bracketed')
        ->result('foobar');

    $s
        ->action('bracketed action with "foobar" result and prefix', '>', 'bracketed')
        ->result('foobar');

    $t = ($s
        ->action('bracketed action with "foobar" result and prefix', '>', 'bracketed')
        ->statusText('some status text'));

    TestCase::assertFalse($t->isInactive());

    $t
        ->finish()
        ->result('foobar');

    TestCase::assertTrue($t->isInactive());

    $s
        ->action('default action with "warn" result', ' -->', 'bracketed')
        ->setSupportExtras(true)
        ->statusText()
            ->text('1st status')
            ->text('2nd status')
            ->finish()
        ->resultWarn()
        ->extras('foo', 'bar', 'baz')
            ->finish()
        ->complete();

    $a = $s
        ->action('downloading remote resource');
    $p = $a
        ->statusProgress(100, 10);

    TestCase::assertFalse($p->isCompleted());

    for ($i = 0; $i < 40; ++$i) {
        TestCase::assertFalse($p->isCompleted());
        $p->progress(2);
    }

    TestCase::assertFalse($p->isCompleted());

    for ($i = 0; $i < 10; ++$i) {
        TestCase::assertFalse($p->isCompleted());
        $p->progress(1);
    }

    TestCase::assertTrue($p->isCompleted());
    TestCase::assertTrue($p->isInactive());

    $a->resultStop();

    $a = $s
        ->action('downloading remote resource');
    $p = $a
        ->statusProgress(100, 10);

    $p->setAutoFinish(false);

    TestCase::assertFalse($p->isCompleted());

    for ($i = 0; $i < 40; ++$i) {
        TestCase::assertFalse($p->isCompleted());
        $p->progress(2);
    }

    TestCase::assertFalse($p->isCompleted());

    for ($i = 0; $i < 10; ++$i) {
        TestCase::assertFalse($p->isCompleted());
        $p->progress(1);
    }

    TestCase::assertTrue($p->isCompleted());
    TestCase::assertFalse($p->isInactive());

    $p->finish();

    $a->resultStop();

    $s
        ->action('custom argument bracketed type', null, BracketedAction::class, [null, null, function (Markup $markup, string $prefix): string {
            return $markup(sprintf('%s ', Markup::createExplicit()($prefix)));
        }, null, function (Markup $markup, string $action): string {
            return $markup(sprintf('%s ... ', Markup::createExplicit()(sprintf('[ %s ]', $action))));
        }, null, function (Markup $markup): string {
            return $markup('(');
        }, null, function (Markup $markup, string $inner): string {
            return $markup($inner);
        }, '.', null, function (Markup $markup): string {
            return $markup(')');
        }, null, function (Markup $markup, string $result): string {
            return $markup(sprintf(' %s ', mb_strtoupper($result)));
        }, null, function (Markup $markup): string {
            return $markup('(');
        }, null, function (Markup $markup, string $character): string {
            return $markup($character);
        }, null, function (Markup $markup): string {
            return $markup(')');
        }])
        ->result('result');
};
