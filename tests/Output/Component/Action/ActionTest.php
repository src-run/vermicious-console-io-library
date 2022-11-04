<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Output\Component\Progress;

use PHPUnit\Framework\TestCase;
use SR\Console\Output\Component\Action\AbstractAction;
use SR\Console\Output\Component\Action\ActionFactory;
use SR\Console\Output\Component\Action\BracketedAction;
use SR\Console\Output\Component\Action\SimpleAction;
use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Console\Output\Exception\RuntimeException;
use SR\Console\Output\Exception\StateException;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Tests\Style\StyleTest;

/**
 * @covers \SR\Console\Output\Component\Action\AbstractAction
 * @covers \SR\Console\Output\Component\Action\ActionFactory
 * @covers \SR\Console\Output\Component\Action\BracketedAction
 * @covers \SR\Console\Output\Component\Action\SimpleAction
 */
class ActionTest extends TestCase
{
    public static function provideActionTypeData(): \Generator
    {
        yield ['bracketed', BracketedAction::class];
        yield ['Bracketed', BracketedAction::class];
        yield ['bracketedAction', BracketedAction::class];
        yield ['BracketedAction', BracketedAction::class];
        yield [BracketedAction::class, BracketedAction::class];
        yield ['simple', SimpleAction::class];
        yield ['Simple', SimpleAction::class];
        yield ['simpleAction', SimpleAction::class];
        yield ['SimpleAction', SimpleAction::class];
        yield [SimpleAction::class, SimpleAction::class];
        yield [null, SimpleAction::class];
    }

    /**
     * @dataProvider provideActionTypeData
     */
    public function testActionFactoryCreate(?string $type, string $class)
    {
        $this->assertInstanceOf($class, ActionFactory::create($type));
    }

    public static function provideInvalidActionTypeData(): \Generator
    {
        yield ['foo'];
        yield ['bar'];
        yield ['brakcetedd'];
        yield ['sIMPLE'];
    }

    /**
     * @dataProvider provideInvalidActionTypeData
     */
    public function testInvalidActionFactoryCreate(string $type)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('{Unable to find action of type class \(none of .+ are valid action types\)\.}');

        ActionFactory::create($type);
    }

    /**
     * @dataProvider provideActionTypeData
     */
    public function testThrowsExceptionWhenExtrasDisabled(string $type = null)
    {
        $action = self::createAction($type);
        $action->setSupportExtras(true);
        $action->action('Action with extras supported');
        $action->extras('one', 'two', 'three');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action extras disabled: enable by passing "true" to "extrasEnabled()"');

        $action = self::createAction($type);
        $action->setSupportExtras(false);
        $action->action('Action with extras supported');
        $action->extras('one', 'two', 'three');
    }

    public function provideMethodCalledFromInvalidStateData(): \Generator
    {
        foreach (self::provideActionTypeData()  as [$type, $class]) {
            yield ['setNewlinesCount', function (AbstractAction $action) {
                $action->setSupportExtras(true);
                $action->setNewlinesCount(10);
                $action->action('');
                $action->result('');
                $action->setNewlinesCount(10);
            }, $type];

            yield ['setSupportExtras', function (AbstractAction $action) {
                $action->setSupportExtras(true);
                $action->action('');
                $action->result('');
                $action->setSupportExtras(false);
            }, $type];

            yield ['progress', function (AbstractAction $action) {
                $action->setStatusProgressCharacter('-');
                $action->action('');
                $action->result('');
                $action->statusProgress(100, 10);
                $action->setStatusProgressCharacter('-');
            }, $type];

            yield ['action', function (AbstractAction $action) {
                $action->action('');
                $action->action('');
            }, $type];

            yield ['result', function (AbstractAction $action) {
                $action->result('');
            }, $type];

            yield ['text', function (AbstractAction $action) {
                $action->setSupportExtras(true);
                $action->extras('');
            }, $type];

            yield ['text', function (AbstractAction $action) {
                $action->setSupportExtras(true);
                $action->action('');
                $action->result('');
                $action->complete();
                $action->extras('');
            }, $type];

            yield ['complete', function (AbstractAction $action) {
                $action->complete();
            }, $type];
        }
    }

    /**
     * @dataProvider provideMethodCalledFromInvalidStateData
     */
    public function testThrowsExceptionWhenMethodCalledFromInvalidState(string $method, \Closure $closure, string $type = null)
    {
        $this->expectException(StateException::class);
        $this->expectExceptionMessageMatches(sprintf(
            '{Cannot call SR\\\Console\\\Output\\\Component\\\Action[a-zA-Z\\\]+::%s\(\) method in state "[^"]+" \(acceptable states: [a-z, -]+\)\.}', $method
        ));

        $closure(self::createAction($type));
    }

    /**
     * @dataProvider provideActionTypeData
     */
    public function testActionStyleAllowsMultipleEndCalls(string $type = null): void
    {
        $a = self::createAction($type)->action('action');
        $s = $a->statusProgress();
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());

        $a = self::createAction($type)->action('action');
        $s = $a->statusProgress(10);
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());

        $a = self::createAction($type)->action('action');
        $s = $a->statusProgress(10, 5);
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());

        $a = self::createAction($type)->action('action');
        $s = $a->statusText();
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());

        $a = self::createAction($type)->action('action');
        $s = $a->statusText('foo', 'bar');
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
        $this->assertInstanceOf(AbstractAction::class, $s->finish());
    }

    private static function createAction(string $type = null, StyleInterface $style = null): AbstractAction
    {
        return ActionFactory::create($type, $style ?? StyleTest::createStyleInstance());
    }
}
