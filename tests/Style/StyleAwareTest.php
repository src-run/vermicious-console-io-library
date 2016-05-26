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

use SR\Console\Style\Style;
use SR\Console\Style\StyleInterface;
use SR\Console\Tests\Output\TestOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StyleAwareTest.
 */
class StyleAwareTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|OutputInterface
     */
    private function mockOutput()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Console\Output\Output')
            ->getMockForAbstractClass();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StyleInterface
     */
    private function mockStyle($input, $output)
    {
        return $this
            ->getMockBuilder('SR\Console\Style\Style')
            ->setConstructorArgs([$input, $output])
            ->getMockForAbstractClass();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StyleAwareFixture
     */
    private function mockFixture()
    {
        $input = $this->mockInput();
        $output = $this->mockOutput();
        $style = $this->mockStyle($input, $output);

        $fixture = new StyleAwareFixture();
        $fixture->setStyle($style);

        return $fixture;
    }

    public function testSetterAndGetter()
    {
        $input = $this->mockInput();
        $output = $this->mockOutput();
        $style = $this->mockStyle($input, $output);

        $fixture = new StyleAwareFixture();
        $fixture->setInput($input);
        $fixture->setOutput($output);
        $this->assertSame($input, $fixture->getInput());
        $this->assertSame($output, $fixture->getOutput());

        $fixture = new StyleAwareFixture();
        $fixture->setStyle($style);
        $this->assertSame($style, $fixture->getStyle());
    }

    public function testIo()
    {
        $fixture = $this->mockFixture();

        $this->assertInstanceOf('SR\Console\Style\StyleInterface', $fixture->styleIo());
        $this->assertInstanceOf('Symfony\Component\Console\Input\InputInterface', $fixture->styleIo()->getInput());
        $this->assertInstanceOf('Symfony\Component\Console\Output\OutputInterface', $fixture->styleIo()->getOutput());
    }

    public function testStyleIo()
    {
        $input = $this->mockInput();
        $output = new TestOutput();
        $style = new Style($input, $output);
        $fixture = new StyleAwareFixture();
        $fixture->setStyle($style);
        $tester = new StyleTester($fixture);

        $tester->execute(
            'io',
            function (StyleInterface $style) {
                $style->text('testStyleIo');
            }
        );

        $this->assertSame('testStyleIo', $tester->getDisplay());
    }
}

/* EOF */
