<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Output\Utility\Terminal;

use PHPUnit\Framework\TestCase;
use SR\Console\Output\Utility\Terminal\Terminal;

/**
 * @covers \SR\Console\Output\Utility\Terminal\Terminal
 */
class TerminalTest extends TestCase
{
    public function textY(): void
    {
        $y = Terminal::y();

        $this->assertInternalType('int', $y);
        $this->assertGreaterThan(0, $y);
        $this->assertSame($y, Terminal::rows());
        $this->assertSame($y, Terminal::height());
    }

    public function testX(): void
    {
        $x = Terminal::x();

        $this->assertInternalType('int', $x);
        $this->assertGreaterThan(0, $x);
        $this->assertSame($x, Terminal::columns());
        $this->assertSame($x, Terminal::width());
    }
}
