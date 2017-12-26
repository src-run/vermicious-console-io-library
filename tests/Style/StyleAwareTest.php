<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) 2016 Rob Frawley 2nd(rmf) <rmf AT src DOT run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Style;

use SR\Console\Output\Style\StyleInterface;
use SR\Console\Tests\Fixtures\StyleAwareExternalClass;
use SR\Console\Tests\Fixtures\StyleAwareInternalClass;
use SR\Console\Tests\Fixtures\StyleAwareLegacyClass;

class StyleAwareTest extends \PHPUnit_Framework_TestCase
{
    public function testStyleAwareInternal()
    {
        $s = new StyleAwareInternalClass();

        $this->assertFalse(is_callable([$s, 'setStyle']));
        $this->assertFalse(is_callable([$s, 'style']));
    }

    public function testStyleAwareExternal()
    {
        $s = new StyleAwareExternalClass();

        $this->assertTrue(is_callable([$s, 'setStyle']));
        $this->assertTrue(is_callable([$s, 'style']));
        $this->assertNull($s->style());

        $s->setStyle(StyleTest::createStyleInstance());
        $this->assertInstanceOf(StyleInterface::class, $s->style());
    }

    /**
     * @group legacy
     */
    public function testStyleLegacyExternal()
    {
        $s = new StyleAwareLegacyClass();

        $this->assertTrue(is_callable([$s, 'setStyle']));
        $this->assertTrue(is_callable([$s, 'getStyle']));
        $this->assertNull($s->getStyle());

        $s->setStyle(StyleTest::createStyleInstance());
        $this->assertInstanceOf(StyleInterface::class, $s->getStyle());
    }
}
