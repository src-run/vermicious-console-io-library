<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) 2016 Rob Frawley 2nd(rmf) <rmf AT src DOT run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Input;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Class InputAwareTest.
 */
class InputAwareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|InputInterface
     */
    private function mockInput()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Console\Input\Input')
            ->getMockForAbstractClass();
    }

    public function testSetterAndGetter()
    {
        $input = $this->mockInput();

        $fixture = new InputAwareFixture();
        $fixture->setInput($input);

        $this->assertSame($input, $fixture->getInput());
    }
}

/* EOF */
