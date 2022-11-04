<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Style;

use PHPUnit\Framework\TestCase;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Tests\Fixtures\StyleAwareExternalClass;
use SR\Console\Tests\Fixtures\StyleAwareInternalClass;

/**
 * @covers \SR\Console\Tests\Fixtures\StyleAwareInternalClass
 * @covers \SR\Console\Tests\Fixtures\StyleAwareExternalClass
 */
class StyleAwareTest extends TestCase
{
    public function testStyleAwareInternal()
    {
        $s = new StyleAwareInternalClass();

        $this->assertIsNotCallable([$s, 'setStyle']);
        $this->assertIsNotCallable([$s, 'style']);
    }

    public function testStyleAwareExternal()
    {
        $s = new StyleAwareExternalClass();

        $this->assertIsCallable([$s, 'setStyle']);
        $this->assertIsCallable([$s, 'style']);
        $this->assertNull($s->style());

        $s->setStyle(StyleTest::createStyleInstance());
        $this->assertInstanceOf(StyleInterface::class, $s->style());
    }
}
