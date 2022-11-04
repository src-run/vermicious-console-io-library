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
use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Markup\MarkupOptions;

/**
 * @covers \SR\Console\Output\Markup\Markup
 */
class MarkupTest extends TestCase
{
    public function testEmptyMarkupAllowed(): void
    {
        $m = new Markup();

        $m->setEmptyMarkupAllowed(false);
        $this->assertSame('inner', $m('inner'));

        $m->setEmptyMarkupAllowed(true);
        $this->assertSame('<>inner</>', $m('inner'));
    }

    public static function provideColorsData(): \Generator
    {
        $colors = Markup::ACCEPTED_COLOURS;

        foreach ($colors as $fg) {
            yield [$fg, null];
            yield [null, $fg];

            foreach ($colors as $bg) {
                yield [$fg, $bg];
            }
        }
    }

    /**
     * @dataProvider provideColorsData
     */
    public function testColors(?string $fg, ?string $bg): void
    {
        $m = new Markup($fg, $bg);
        $this->assertMarkupColors($m, $fg, $bg);

        $m->setColourExplicit(true);
        $this->assertMarkupColors($m, $fg, $bg, true);
    }

    public static function provideOptionsData(): \Generator
    {
        yield [Markup::O_BOLD];
        yield [Markup::O_REVERSE];
        yield [Markup::O_BLINK];
        yield [Markup::O_UNDERSCORE];
        yield [Markup::O_BOLD, Markup::O_REVERSE, Markup::O_BLINK, Markup::O_UNDERSCORE];
    }

    /**
     * @dataProvider provideOptionsData
     */
    public function testOptions(?string ...$options): void
    {
        ($m = new Markup())->setOptions(...$options);

        foreach ($options as $opt) {
            $this->assertMatchesRegularExpression(sprintf('{options=([a-z,]+)?%s([a-z,]+)?}', preg_quote($opt)), $m('inner'));
        }
    }

    public static function provideInvalidOptionsData(): \Generator
    {
        yield [''];
        yield [MarkupOptions::O_BOLD, ''];
        yield ['invalid-option'];
        yield [MarkupOptions::O_BOLD, 'invalid-option'];
        yield ['!@#$%^&*()'];
        yield [MarkupOptions::O_BOLD, '!@#$%^&*()'];
    }

    /**
     * @dataProvider provideInvalidOptionsData
     */
    public function testInvalidOptions(?string ...$options): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid option name provided:');

        (new Markup())->setOptions(...$options);
    }

    private function assertMarkupColors(Markup $markup, ?string $fg, ?string $bg, bool $explicit = false): void
    {
        if (null === $fg && false === $explicit) {
            $this->assertStringNotContainsString(sprintf('fg=%s', $fg), $markup('inner'));
        } else {
            $this->assertStringContainsString(sprintf('fg=%s', $fg ?? Markup::C_DEFAULT), $markup('inner'));
        }

        if (null === $bg && false === $explicit) {
            $this->assertStringNotContainsString(sprintf('bg=%s', $bg), $markup('inner'));
        } else {
            $this->assertStringContainsString(sprintf('bg=%s', $bg ?? Markup::C_DEFAULT), $markup('inner'));
        }
    }
}
