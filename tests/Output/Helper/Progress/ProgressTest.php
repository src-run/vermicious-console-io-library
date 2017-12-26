<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) 2016 Rob Frawley 2nd(rmf) <rmf AT src DOT run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Output\Helper\Progress;

use SR\Console\Output\Helper\Progress\ConcisePercentageProgressHelper;
use SR\Console\Output\Helper\Progress\DefaultProgressHelper;
use SR\Console\Tests\Style\StyleTest;

class ProgressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \SR\Exception\Logic\InvalidArgumentException
     * @expectedExceptionMessage Percent based progress helper is hard-coded to use 100 steps; do not pass step count to
     */
    public function testPercentageProgressOutOfBoundsCreate()
    {
        $h = new ConcisePercentageProgressHelper(StyleTest::createStyleInstance());
        $h->create(1000);
    }

    /**
     * @expectedException \SR\Exception\Runtime\RuntimeException
     * @expectedExceptionMessage You must start an active progress bar before acting on it!
     */
    public function testStepBeforeCreate()
    {
        $h = new DefaultProgressHelper(StyleTest::createStyleInstance());
        $h->step();
    }

    /**
     * @expectedException \SR\Exception\Runtime\RuntimeException
     * @expectedExceptionMessage You must stop an active progress bar before starting a new one!
     */
    public function testCreateWhileStepping()
    {
        $h = new DefaultProgressHelper(StyleTest::createStyleInstance());
        $h->create(10);
        $h->step();
        $h->create(10);
    }
}
