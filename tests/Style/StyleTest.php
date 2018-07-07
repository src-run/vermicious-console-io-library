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
use SR\Console\Output\Component\Block\Block;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\Style;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Tests\Input\MemoryInput;
use SR\Console\Tests\Output\TestOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \SR\Console\Input\Component\Question\Answer\AbstractAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\BooleanAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\ChoiceAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\MultipleChoiceAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\ScalarAnswer
 * @covers \SR\Console\Input\Component\Question\QuestionHelper
 * @covers \SR\Console\Output\Component\Action\AbstractAction
 * @covers \SR\Console\Output\Component\Action\ActionFactory
 * @covers \SR\Console\Output\Component\Action\BracketedAction
 * @covers \SR\Console\Output\Component\Action\SimpleAction
 * @covers \SR\Console\Output\Component\Header\SectionHeader
 * @covers \SR\Console\Output\Component\Header\TitleHeader
 * @covers \SR\Console\Output\Component\Listing\DefinitionList
 * @covers \SR\Console\Output\Component\Listing\SimpleList
 * @covers \SR\Console\Output\Component\Progress\Message\ProgressMessageHelper
 * @covers \SR\Console\Output\Component\Progress\AbstractPercentageProgress
 * @covers \SR\Console\Output\Component\Progress\AbstractProgressHelper
 * @covers \SR\Console\Output\Component\Progress\ConcisePercentageProgress
 * @covers \SR\Console\Output\Component\Progress\ConciseProgress
 * @covers \SR\Console\Output\Component\Progress\DefaultProgress
 * @covers \SR\Console\Output\Component\Progress\VerbosePercentageProgress
 * @covers \SR\Console\Output\Component\Progress\VerboseProgress
 * @covers \SR\Console\Output\Component\Table\AbstractTable
 * @covers \SR\Console\Output\Component\Table\HorizontalTable
 * @covers \SR\Console\Output\Component\Table\VerticalTable
 * @covers \SR\Console\Output\Component\Block\Block
 * @covers \SR\Console\Output\Component\Text\Text
 * @covers \SR\Console\Output\Markup\Markup
 * @covers \SR\Console\Output\Style\Style
 * @covers \SR\Console\Tests\Fixtures\StyleAwareExternalClass
 * @covers \SR\Console\Tests\Fixtures\StyleAwareInternalClass
 * @covers \SR\Console\Output\Utility\Interpolate\AbstractStringInterpolator
 * @covers \SR\Console\Output\Utility\Interpolate\PsrStringInterpolator
 * @covers \SR\Console\Output\Utility\Interpolate\SpfStringInterpolator
 * @covers \SR\Console\Output\Utility\Interpolate\StringInterpolatorTrait
 */
class StyleTest extends TestCase
{
    /**
     * @var string
     */
    private static $fixtureRootPath = __DIR__.'/../Fixtures/Style/';

    public function testInputOutputAccessorMethods(): void
    {
        $s = static::createStyleInstance($i = static::createInputInstance(), $o = static::createOutputInstance());

        $this->assertSame($i, $s->getInput());
        $this->assertSame($o, $s->getOutput());
    }

    /**
     * @return array
     */
    public static function provideVerbosityAccessorMethodData(): array
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
     * @dataProvider provideVerbosityAccessorMethodData
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

        if (StyleInterface::VERBOSITY_QUIET === $verbosity) {
            $this->assertTrue($s->isQuiet());
        } else {
            $this->assertFalse($s->isQuiet());
        }
    }

    public function testDecorationAccessorMethods(): void
    {
        $s = static::createStyleInstance();

        $this->assertFalse($s->getOutput()->isDecorated());
        $s->setDecorated(true);
        $this->assertTrue($s->getOutput()->isDecorated());
    }

    public function testFormatterAccessorMethods(): void
    {
        $s = static::createStyleInstance();
        $f = new OutputFormatter();
        $s->setFormatter($f);

        $this->assertSame($f, $s->getFormatter());
    }

    public function testDecorationHelper(): void
    {
        $d = new Markup();
        $d->setForeground('white');
        $d->setBackground('magenta');
        $d->setOptions('bold', 'reverse');

        $this->assertSame('<fg=white;bg=magenta;options=bold,reverse>A string to decorate</>', $d->markupValue('A string to decorate'));
    }

    public function testThrowsOnInvalidTableHeaderRowsCount(): void
    {
        $this->expectException(\SR\Exception\Logic\InvalidArgumentException::class);
        $this->expectExceptionMessage('Header count does not match row count!');

        $s = static::createStyleInstance();
        $s->tableVertical([
            'header 1',
        ], [
            'row 1a',
        ], [
            'row 1b',
        ]);
    }

    public function testQuestionDefaults(): void
    {
        $s = static::createStyleInstance($i = static::createInputInstance());
        $i->setInteractive(false);

        $this->assertSame('A default answer', $s->ask('A question', 'A default answer')->getAnswer());
        $this->assertSame('A default answer', $s->askHidden('A hidden question', 'A default answer')->getAnswer());
        $this->assertFalse($s->confirm('A confirmation', false)->getAnswer());
        $this->assertTrue($s->confirm('A confirmation', true)->getAnswer());

        $choices = ['a' => 'Apple', 'p' => 'Pear', 'b' => 'Banana'];
        foreach ($choices as $index => $value) {
            $this->assertSame($value, $s->choice('A choice', $choices, $value)->getAnswer());
        }
    }

    /**
     * @return array
     */
    public static function provideStyleTestResourcesData(): array
    {
        $closurePaths = glob(static::$fixtureRootPath.'/*/closure.php');
        $consolePaths = glob(static::$fixtureRootPath.'/*/console.txt');

        return array_map(null, $closurePaths, $consolePaths);
    }

    /**
     * @param string $commandFile
     * @param string $outputFile
     *
     * @dataProvider provideStyleTestResourcesData
     */
    public function testCommandOutputBuffer(string $commandFile, string $outputFile)
    {
        $fuzzy = false;

        foreach (['progress', 'question', 'title', 'functional'] as $type) {
            if (false !== mb_strpos($outputFile, $type)) {
                $fuzzy = true;
            }
        }

        $this->assertStringFuzzyEqualsFile($outputFile, $this->setAndExecuteCommandTest(require $commandFile), $commandFile, $fuzzy);
    }

    /**
     * @return array[]
     */
    public static function dataBlockWordWrappingProvider(): array
    {
        $return = [];
        $string = [];
        $blocks = [
            [Block::TYPE_SM, 0],
            [Block::TYPE_MD, 0],
            [Block::TYPE_LG, 1],
        ];

        foreach (range(2, 4, 2) as $repeat) {
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
        $inputLength = mb_strlen($inputString);
        $needleChars = ' § ';
        $needleLines = (int) (ceil($inputLength / 80) + ($inputLength > 80 - 5) + $lineAdjustment);

        $result = $this->setAndExecuteCommandTest(function (InputInterface $input, OutputInterface $output) use ($inputString, $blockType, $needleChars) {
            (new Style($input, $output, 80))->block($inputString, 'TEST', [], $blockType, $needleChars);
        });

        $this->assertSame($needleLines, mb_substr_count($result, $needleChars));
    }

    /**
     * @param InputInterface|null  $i
     * @param OutputInterface|null $o
     *
     * @return StyleInterface
     */
    public static function createStyleInstance(InputInterface $i = null, OutputInterface $o = null): StyleInterface
    {
        return new Style($i ?: static::createInputInstance(), $o ?: static::createOutputInstance());
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
                'decorated' => false,
            ];
        }

        $command = new Command(sprintf('src-run-style-%s', spl_object_hash($code)));
        $command->setCode($code);

        $tester = new CommandTester($command);
        $tester->execute([], $options);

        return $tester->getDisplay($normalize);
    }

    /**
     * @return InputInterface
     */
    private static function createInputInstance(): InputInterface
    {
        return new MemoryInput();
    }

    /**
     * @return OutputInterface
     */
    private static function createOutputInstance(): OutputInterface
    {
        return new TestOutput();
    }

    /**
     * @param string $expectedFile
     * @param string $actualString
     * @param string $message
     * @param bool   $fuzzy
     */
    private static function assertStringFuzzyEqualsFile($expectedFile, $actualString, $message = '', bool $fuzzy = false)
    {
        static::assertFileExists($expectedFile, $message);

        if (false === $fuzzy) {
            static::assertStringEqualsFile($expectedFile, $actualString, $message);

            return;
        }

        $expectedLines = explode("\n", file_get_contents($expectedFile));
        $providedLines = explode("\n", $actualString);

        for ($i = 0; $i < count($expectedLines); ++$i) {
            static::assertLineFuzzyEqualsLine($expectedLines[$i] ?? null, $providedLines[$i] ?? null, $i + 1, $expectedFile);
        }
    }

    /**
     * @param string $expectedText
     * @param string $providedText
     * @param int    $fileLine
     * @param string $fileName
     */
    private static function assertLineFuzzyEqualsLine($expectedText, $providedText, int $fileLine, string $fileName)
    {
        if (1 === preg_match('{^\{.+\}$}', $expectedText)) {
            self::assertGeneratedOutputEqualsExpectedRegExp($expectedText, $providedText, $fileLine, $fileName);
        } else {
            self::assertGeneratedOutputEqualsExpectedOutput($expectedText, $providedText, $fileLine, $fileName);
        }
    }

    /**
     * @param string $expectedOutput
     * @param string $providedOutput
     * @param int    $fileLine
     * @param string $fileName
     */
    private static function assertGeneratedOutputEqualsExpectedOutput($expectedOutput, $providedOutput, int $fileLine, string $fileName)
    {
        static::assertSame($expectedOutput, $providedOutput, vsprintf(
            '%sExpectation from line "%d" of file "%s" (as explicit text) does not match generated text.%1$s  [expected output] => "%s"%1$s  [provided output] => "%s"%1$s', [
                PHP_EOL,
                $fileLine,
                $fileName,
                $expectedOutput,
                $providedOutput,
            ]
        ));
    }

    /**
     * @param string $expectedRegExp
     * @param string $providedOutput
     * @param int    $fileLine
     * @param string $fileName
     */
    private static function assertGeneratedOutputEqualsExpectedRegExp($expectedRegExp, $providedOutput, int $fileLine, string $fileName)
    {
        static::assertRegExp($expectedRegExp, $providedOutput, vsprintf(
            '%sExpectation from line "%d" of file "%s" (as regular expression) does not match generated text.%1$s  [expected regexp] => "%s"%1$s  [provided output] => "%s"%1$s', [
                PHP_EOL,
                $fileLine,
                $fileName,
                $expectedRegExp,
                $providedOutput,
            ]
        ));
    }
}
