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
use SR\Console\Output\Component\Progress\ConcisePercentageProgress;
use SR\Console\Output\Component\Progress\DefaultProgress;
use SR\Console\Tests\Style\StyleTest;

/**
 * @covers \SR\Console\Output\Component\Progress\Message\ProgressMessageHelper
 * @covers \SR\Console\Output\Component\Progress\AbstractPercentageProgress
 * @covers \SR\Console\Output\Component\Progress\AbstractProgressHelper
 * @covers \SR\Console\Output\Component\Progress\ConcisePercentageProgress
 * @covers \SR\Console\Output\Component\Progress\ConciseProgress
 * @covers \SR\Console\Output\Component\Progress\DefaultProgress
 * @covers \SR\Console\Output\Component\Progress\VerbosePercentageProgress
 * @covers \SR\Console\Output\Component\Progress\VerboseProgress
 */
class ProgressTest extends TestCase
{
    public function testPercentageProgressOutOfBoundsCreate()
    {
        $this->expectException(\SR\Exception\Logic\InvalidArgumentException::class);
        $this->expectExceptionMessage('Percent based progress helper is hard-coded to use 100 steps; do not pass step count to');

        $h = new ConcisePercentageProgress(StyleTest::createStyleInstance());
        $h->create(1000);
    }

    public function testStepBeforeCreate()
    {
        $this->expectException(\SR\Exception\Runtime\RuntimeException::class);
        $this->expectExceptionMessage('You must start an active progress bar before acting on it!');

        $h = new DefaultProgress(StyleTest::createStyleInstance());
        $h->step();
    }

    public function testCreateWhileStepping()
    {
        $this->expectException(\SR\Exception\Runtime\RuntimeException::class);
        $this->expectExceptionMessage('You must stop an active progress bar before starting a new one!');

        $h = new DefaultProgress(StyleTest::createStyleInstance());
        $h->create(10);
        $h->step();
        $h->create(10);
    }
}
