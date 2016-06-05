<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) 2016 Rob Frawley 2nd(rmf) <rmf AT src DOT run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Output;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OutputAwareTest.
 */
class OutputAwareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|OutputInterface
     */
    private function mockOutput()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Console\Output\Output')
            ->getMockForAbstractClass();
    }

    public function testSetterAndGetter()
    {
        $output = $this->mockOutput();

        $fixture = new OutputAwareFixture();
        $fixture->setOutput($output);

        $this->assertSame($output, $fixture->getOutput());
    }
}

/* EOF */
