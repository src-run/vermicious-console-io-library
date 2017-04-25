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

use SR\Console\Output\Helper\BlockHelper;
use SR\Console\Output\Helper\DecorationHelper;
use SR\Console\Output\Style\Style;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Tests\Input\TestInput;
use SR\Console\Tests\Output\TestOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class StyleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private static $fixtureRootPath = __DIR__.'/../Fixtures/Style/';

    public function testInputOutputAccessorMethods()
    {
        $s = static::createStyleInstance($i = static::createInputInstance(), $o = static::createOutputInstance());

        $this->assertSame($i, $s->getInput());
        $this->assertSame($o, $s->getOutput());
    }

    public static function dataVerbosityAccessorMethodProvider()
    {
        return [
            [StyleInterface::VERBOSITY_QUIET],
            [StyleInterface::VERBOSITY_NORMAL],
            [StyleInterface::VERBOSITY_VERBOSE],
            [StyleInterface::VERBOSITY_VERY_VERBOSE],
            [StyleInterface::VERBOSITY_DEBUG],
        ];
    }

    /**
     * @param int $verbosity
     *
     * @dataProvider dataVerbosityAccessorMethodProvider
     */
    public function testVerbosityAccessorMethods(int $verbosity)
    {
        $s = static::createStyleInstance(null, $o = new TestOutput());

        $this->assertSame(StyleInterface::VERBOSITY_NORMAL, $s->getVerbosity());
        $o->setVerbosity($verbosity);
        $this->assertSame($verbosity, $s->getVerbosity());

        if ($verbosity >= StyleInterface::VERBOSITY_DEBUG) {
            $this->assertTrue($s->isDebug());
        } else {
            $this->assertFalse($s->isDebug());
        }

        if ($verbosity >= StyleInterface::VERBOSITY_VERY_VERBOSE) {
            $this->assertTrue($s->isVeryVerbose());
        } else {
            $this->assertFalse($s->isVeryVerbose());
        }

        if ($verbosity >= StyleInterface::VERBOSITY_VERBOSE) {
            $this->assertTrue($s->isVerbose());
        } else {
            $this->assertFalse($s->isVerbose());
        }

        if ($verbosity === StyleInterface::VERBOSITY_QUIET) {
            $this->assertTrue($s->isQuiet());
        } else {
            $this->assertFalse($s->isQuiet());
        }
    }

    public function testDecorationAccessorMethods()
    {
        $s = static::createStyleInstance();

        $this->assertFalse($s->getOutput()->isDecorated());
        $s->setDecorated(true);
        $this->assertTrue($s->getOutput()->isDecorated());
    }

    public function testFormatterAccessorMethods()
    {
        $s = static::createStyleInstance();
        $f = new OutputFormatter();
        $s->setFormatter($f);

        $this->assertSame($f, $s->getFormatter());
    }

    public function testTermDimensionsAccessorMethods()
    {
        $s = static::createStyleInstance();

        $this->assertInternalType('int', $s->termHeight());
        $this->assertGreaterThan(0, $s->termHeight());
        $this->assertInternalType('int', $s->termWidth());
        $this->assertGreaterThan(0, $s->termWidth());
    }

    public function testDecorationHelper()
    {
        $d = new DecorationHelper();
        $d->setForeground('white');
        $d->setBackground('magenta');
        $d->setOptions('bold', 'reverse');

        $this->assertSame('<fg=white;bg=magenta;options=bold,reverse>A string to decorate</>', $d->decorate('A string to decorate'));
    }

    /**
     * @expectedException \SR\Exception\Logic\InvalidArgumentException
     * @expectedExceptionMessage Header count does not match row count!
     */
    public function testThrowsOnInvalidTableHeaderRowsCount()
    {
        $s = static::createStyleInstance();
        $s->tableVertical([
            'header 1',
        ], [
            'row 1a',
        ], [
            'row 1b',
        ]);
    }

    public function testQuestionDefaults()
    {
        $s = static::createStyleInstance($i = static::createInputInstance());
        $i->setInteractive(false);

        $this->assertSame('A default answer', $s->ask('A question', 'A default answer'));
        $this->assertSame('A default answer', $s->askHidden('A hidden question', 'A default answer'));
        $this->assertFalse($s->confirm('A confirmation', false));
        $this->assertTrue($s->confirm('A confirmation', true));

        $choices = ['a' => 'Apple', 'p' => 'Pear', 'b' => 'Banana'];
        foreach ($choices as $index => $value) {
            $this->assertSame($index, $s->choice('A choice', $choices, $value));
        }
    }

    /**
     * @return array
     */
    public static function dataCommandOutputBufferProvider(): array
    {
        $closurePaths = glob(static::$fixtureRootPath.'/*/closure.php');
        $consolePaths = glob(static::$fixtureRootPath.'/*/console.txt');

        return array_map(null, $closurePaths, $consolePaths);
    }

    /**
     * @param string $commandFile
     * @param string $outputFile
     *
     * @dataProvider dataCommandOutputBufferProvider
     */
    public function testCommandOutputBuffer(string $commandFile, string $outputFile)
    {
        $this->assertStringEqualsFile($outputFile, $this->setAndExecuteCommandTest(require $commandFile), $commandFile);
    }

    /**
     * @return array[]
     */
    public static function dataBlockWordWrappingProvider(): array
    {
        $return = [];
        $string = [];
        $blocks = [
            [BlockHelper::TYPE_SM, 0],
            [BlockHelper::TYPE_MD, 0],
            [BlockHelper::TYPE_LG, 1],
        ];

        foreach (range(2, 12, 2) as $repeat) {
            $string[] = str_repeat('Lopadotemachoselachogaleokranioleipsanodrimhypotrimmatosilphioparaomelitokatakechymenokichl', $repeat);
        }

        foreach ($string as $s) {
            foreach ($blocks as $b) {
                $return[] = array_merge((array) $s, $b);
            }
        }

        return $return;
    }

    /**
     * @param string $inputString
     * @param int    $blockType
     * @param int    $lineAdjustment
     *
     * @dataProvider dataBlockWordWrappingProvider
     */
    public function testBlockWordWrapping(string $inputString, int $blockType, int $lineAdjustment)
    {
        return;
        $inputLength = strlen($inputString);
        $needleChars = ' § ';
        $needleLines = (int) (ceil($inputLength / 120) + ($inputLength > 120 - 5) + $lineAdjustment);

        $result = $this->setAndExecuteCommandTest(function (InputInterface $input, OutputInterface $output) use ($inputString, $blockType, $needleChars) {
            (new Style($input, $output, 120))->block($inputString, 'TEST', $blockType, $needleChars);
        });

        $this->assertSame($needleLines, substr_count($result, $needleChars));
    }

    /**
     * @param \Closure $code
     * @param bool     $normalize
     * @param array    $options
     *
     * @return string
     */
    private function setAndExecuteCommandTest(\Closure $code, bool $normalize = true, array $options = null): string
    {
        if (null === $options) {
            $options = [
                'interactive' => false,
                'decorated'   => false
            ];
        }

        $command = new Command(sprintf('src-run-style-%s', spl_object_hash($code)));
        $command->setCode($code);

        $tester = new CommandTester($command);
        $tester->execute([], $options);

        return $tester->getDisplay($normalize);
    }

    /**
     * @param InputInterface|null  $i
     * @param OutputInterface|null $o
     *
     * @return StyleInterface
     */
    private static function createStyleInstance(InputInterface $i = null, OutputInterface $o = null): StyleInterface
    {
        return new Style($i ?: static::createInputInstance(), $o ?: static::createOutputInstance());
    }

    /**
     * @return InputInterface
     */
    private static function createInputInstance(): InputInterface
    {
        return new TestInput();
    }

    /**
     * @return OutputInterface
     */
    private static function createOutputInstance(): OutputInterface
    {
        return new TestOutput();
    }
}
