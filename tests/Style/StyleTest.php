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

use SR\Console\Input\Component\Question\Answer\AnswerInterface;
use SR\Console\Input\Component\Question\Answer\BooleanAnswer;
use SR\Console\Input\Component\Question\Answer\ChoiceAnswer;
use SR\Console\Input\Component\Question\Answer\MultipleChoiceAnswer;
use SR\Console\Input\Component\Question\Answer\StringAnswer;
use SR\Console\Output\Component\Block\Block;
use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Console\Output\Exception\RuntimeException;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\Style;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Tests\AbstractTestCase;
use SR\Console\Tests\Input\MemoryInput;
use SR\Console\Tests\Output\TestOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \SR\Console\Input\InputAwareTrait
 * @covers \SR\Console\Input\Component\Question\Answer\AnswerTrait
 * @covers \SR\Console\Input\Component\Question\Answer\BooleanAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\ChoiceAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\MultipleChoiceAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\StringAnswer
 * @covers \SR\Console\Input\Component\Question\QuestionHelper
 * @covers \SR\Console\Output\OutputAwareTrait
 * @covers \SR\Console\Output\Component\Action\Style\Extras\AbstractExtras
 * @covers \SR\Console\Output\Component\Action\Style\Extras\ExtrasText
 * @covers \SR\Console\Output\Component\Action\Style\Status\AbstractStatus
 * @covers \SR\Console\Output\Component\Action\Style\Status\StatusProgress
 * @covers \SR\Console\Output\Component\Action\Style\Status\StatusText
 * @covers \SR\Console\Output\Component\Action\Style\ActionStyleTrait
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
 * @covers \SR\Console\Output\Style\StyleAwareExternalTrait
 * @covers \SR\Console\Output\Style\StyleAwareInternalTrait
 * @covers \SR\Console\Tests\Fixtures\StyleAwareExternalClass
 * @covers \SR\Console\Tests\Fixtures\StyleAwareInternalClass
 * @covers \SR\Console\Output\Utility\Interpolate\AbstractStringInterpolator
 * @covers \SR\Console\Output\Utility\Interpolate\PsrStringInterpolator
 * @covers \SR\Console\Output\Utility\Interpolate\PsrStringInterpolatorTrait
 * @covers \SR\Console\Output\Utility\State\State
 */
class StyleTest extends AbstractTestCase
{
    /**
     * @var string
     */
    private static $fixtureRootPath = __DIR__ . '/../Fixtures/Style/';

    public function testInputOutputAccessorMethods(): void
    {
        $s = static::createStyleInstance($i = static::createInputInstance(), $o = static::createOutputInstance());

        $this->assertSame($i, $s->getInput());
        $this->assertSame($o, $s->getOutput());
    }

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

    public function testQuestionDefault(): void
    {
        $s = static::createStyleInstance($i = static::createInputInstance());
        $i->setInteractive(false);

        $this->assertSame('A default answer', $s->ask('A question', 'A default answer')->getAnswer());
        $this->assertSame('A default answer', $s->askHidden('A hidden question', 'A default answer')->getAnswer());
        $this->assertNonInteractiveBooleanAnswerAccessors($s->confirm('A confirmation', false), false);
        $this->assertNonInteractiveBooleanAnswerAccessors($s->confirm('A confirmation', true), true);
    }

    public function testHiddenQuestion(): void
    {
        $i = new MemoryInput();
        $i->setInteractive(true);
        $i->setInput([
            'password',
        ]);

        /** @var StringAnswer $c */
        $c = static::createStyleInstance($i)->askHidden('What is your password?');

        $this->assertSame('password', $c->getAnswer());
    }

    public function testNonInteractiveNonDefaultQuestion(): void
    {
        $i = new MemoryInput();
        $i->setInteractive(false);

        /** @var StringAnswer $c */
        $c = static::createStyleInstance($i)->askHidden('What is nothing?');

        $this->assertNull($c->getAnswer());
    }

    public static function provideChoiceData(): \Generator
    {
        $choices = self::getCandies();

        foreach ($choices as $index => $value) {
            yield [$choices, $value, $index];
        }
    }

    /**
     * @dataProvider provideChoiceData
     */
    public function testAssociativeChoiceDefault(array $choices, string $value, mixed $index): void
    {
        $index = (string) $index;

        $style = static::createStyleInstance($input = new MemoryInput());
        $input->setInteractive(false);

        $q = 'Which candy would you like to eat right now?';

        $this->assertValidSingleChoiceAnswer($style->choice($q, $choices, $value), $q, $choices, $value, $index);
        $this->assertValidSingleChoiceAnswer($style->choice($q, $choices, $index), $q, $choices, $value, $index);

        $input->setInteractive(true);
        $input->setInput([
            '',
            '',
        ]);

        $this->assertValidSingleChoiceAnswer($style->choice($q, $choices, $value), $q, $choices, $value, $index);
        $this->assertValidSingleChoiceAnswer($style->choice($q, $choices, $index), $q, $choices, $value, $index);
    }

    /**
     * @dataProvider provideChoiceData
     */
    public function testInvalidChoiceDefault(array $choices): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('{Configured default "[^"]+" is not an available choice\.}');

        $i = static::createInputInstance();
        $i->setInteractive(false);

        static::createStyleInstance($i)->choice('Choice with invalid default', $choices, 'invalid-default-choice');
    }

    /**
     * @dataProvider provideChoiceData
     */
    public function testHiddenChoice(array $choices, string $input): void
    {
        $i = new MemoryInput();
        $i->setInteractive(true);
        $i->setInput([
            $input,
        ]);

        /** @var ChoiceAnswer $c */
        $c = static::createStyleInstance($i)->hiddenChoice('What are your favorite hidden candies?', $choices);

        $this->assertInstanceOf(ChoiceAnswer::class, $c);
        $this->assertSame($input, $c->getAnswer());
        $this->assertTrue($c->hasIndex());
        $this->assertSame(array_search($c->getAnswer(), $choices, true), $c->getIndex());
    }

    public static function provideMultipleChoiceData(): \Generator
    {
        $candies = self::getCandies();

        for ($i = 0; $i < 10; ++$i) {
            $inputs = $candies;
            shuffle($inputs);

            yield [$candies, array_slice($inputs, 0, mt_rand(1, count($candies)))];
        }
    }

    /**
     * @dataProvider provideMultipleChoiceData
     */
    public function testMultipleChoice(array $choices, array $inputs): void
    {
        $i = new MemoryInput();
        $i->setInteractive(true);
        $i->setInput([
            implode(',', $inputs),
        ]);

        /** @var MultipleChoiceAnswer $c */
        $c = static::createStyleInstance($i)->multipleChoice('What are your favorite candies?', $choices);

        $this->assertMultipleChoiceAnswerAccessors($c, $choices, $inputs);
    }

    /**
     * @dataProvider provideMultipleChoiceData
     */
    public function testDefaultMultipleChoiceInteractive(array $choices, array $inputs): void
    {
        $i = new MemoryInput();
        $i->setInteractive(true);
        $i->setInput([
            '',
        ]);

        /** @var MultipleChoiceAnswer $c */
        $c = static::createStyleInstance($i)->multipleChoice('What are your favorite default candies?', $choices, implode(',', $inputs));

        $this->assertMultipleChoiceAnswerAccessors($c, $choices, $inputs);
    }

    /**
     * @dataProvider provideMultipleChoiceData
     */
    public function testDefaultMultipleChoiceNonInteractive(array $choices, array $inputs): void
    {
        $i = new MemoryInput();
        $i->setInteractive(false);

        /** @var MultipleChoiceAnswer $c */
        $c = static::createStyleInstance($i)->multipleChoice('What are your favorite default candies?', $choices, implode(',', $inputs));
        $c = static::createStyleInstance($i)->multipleChoice('What are your favorite default candies?', $choices, implode(',', $inputs));

        $this->assertMultipleChoiceAnswerAccessors($c, $choices, $inputs);
    }

    /**
     * @dataProvider provideMultipleChoiceData
     */
    public function testMultipleChoiceInvalidAndValid(array $choices, array $inputs): void
    {
        $i = new MemoryInput();
        $i->setInteractive(true);
        $i->setInput([
            '',
            'foo',
            implode(',', $inputs),
        ]);

        $o = self::createOutputStream();

        /** @var MultipleChoiceAnswer $c */
        $c = static::createStyleInstance($i, $o)->multipleChoice('What are your favorite candies?', $choices);

        $this->assertMultipleChoiceAnswerAccessors($c, $choices, $inputs);

        $stream = $o->getStream();
        rewind($stream);
        $this->assertMatchesRegularExpression('{Invalid.+\n?.*empty.+\n?.*choice.+\n?.*answer.+\n?.*provided\..+\n?.*Available.+\n?.*choices:}', $contents = stream_get_contents($stream));
        $this->assertMatchesRegularExpression('{Invalid.+\n?.*choice.+\n?.*answer.+\n?.*"foo".+\n?.*provided\..+\n?.*Available.+\n?.*choices:}', $contents);
    }

    public function testAmbiguouisChoice(): void
    {
        $choices = [
            'foo' => 'bar',
            'bar' => 'foo',
            'baz' => 'baz',
        ];

        $i = new MemoryInput();
        $i->setInteractive(true);
        $i->setInput([
            'foo',
            'baz',
        ]);

        $o = self::createOutputStream();

        /** @var MultipleChoiceAnswer $c */
        $c = static::createStyleInstance($i, $o)->multipleChoice('What are your favorite candies?', $choices);

        $this->assertMultipleChoiceAnswerAccessors($c, $choices, ['baz']);

        $stream = $o->getStream();
        rewind($stream);
        $this->assertMatchesRegularExpression('{The.+\n?.*provided.+\n?.*answer.+\n?.*is.+\n?.*ambiguous\..+\n?.*Value.+\n?.*should.+\n?.*be.+\n?.*one.+\n?.*of}', stream_get_contents($stream));
    }

    public function testAutoCompletion(): void
    {
        $choices = [
            'aaaaaa',
            'aaabbb',
            'aaaccc',
            'aaaddd',
            'bbbaaa',
            'bbbbbb',
            'bbbccc',
            'bbbddd',
        ];

        $i = new MemoryInput();
        $i->setInteractive(true);
        $i->setInput([
            'z',
            "bbbd\t\177\177\177\t\033[B\177",
            "aaaccc\177\177\177\177a\t\033[A\033[A\033[A\033[B\033[B",
        ]);

        $o = self::createOutputStream();

        /** @var MultipleChoiceAnswer $c */
        $c = static::createStyleInstance($i, $o)->choice('What are your favorite candy?', $choices, null, null, null, $choices);

        $this->assertFalse($c->isDefault());
        $this->assertTrue($c->isInteractive());
        $this->assertSame('aaaaaa', $c->getAnswer());

        $stream = $o->getStream();
        rewind($stream);
        $content = stream_get_contents($stream);
        $this->assertMatchesRegularExpression('{Invalid.+\n?.*choice.+\n?.*answer.+\n?.*"z".+\n?.*provided}', $content);
        $this->assertMatchesRegularExpression('{Invalid.+\n?.*choice.+\n?.*answer.+\n?.*"bb".+\n?.*provided}', $content);
    }

    public static function provideAmbiguousMultipleChoiceAnswerSearchData(): \Generator
    {
        foreach (self::provideMultipleChoiceData() as [$choices, $inputs]) {
            if (count($inputs) > 1) {
                yield [$choices, $inputs];
            }
        }
    }

    /**
     * @dataProvider provideAmbiguousMultipleChoiceAnswerSearchData
     */
    public function testAmbiguousMultipleChoiceAnswerSearch(array $choices, array $inputs): void
    {
        $i = new MemoryInput();
        $i->setInteractive(true);
        $i->setInput([
            implode(',', $inputs),
        ]);

        /** @var MultipleChoiceAnswer $c */
        $c = static::createStyleInstance($i)->multipleChoice('What are your favorite candies?', $choices);

        $this->assertCount($c->count(), $inputs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('{Search was ambiguous and returned [0-9]+ results\.}');

        $this->assertNull($c->findAnswer(function ($a) {
            return true;
        }));
    }

    public static function provideStyleTestResourcesData(): array
    {
        $closurePaths = glob(static::$fixtureRootPath . '/*/*.php');
        $consolePaths = glob(static::$fixtureRootPath . '/*/*.txt');

        return array_map(null, $closurePaths, $consolePaths);
    }

    /**
     * @dataProvider provideStyleTestResourcesData
     */
    public function testCommandOutputBuffer(string $commandFile, string $expectedFile)
    {
        $generatedOut = $this->setAndExecuteCommandTest(require $commandFile);

        static::assertFileExists($expectedFile);

        if (1 === preg_match('{^\{[^\n]+\}$}m', $expectedBlob = file_get_contents($expectedFile), $matches)) {
            $providedLines = explode("\n", $generatedOut);
            $expectedLines = explode("\n", $expectedBlob);

            $this->assertSame(
                $expectedLinesCount = count($expectedLines),
                $providedLinesCount = count($providedLines),
                vsprintf("Output lines from (%s:%d) do not match expected lines from (%s:%d).\n%s\n%s\n", [
                    $commandFile,
                    $providedLinesCount,
                    $expectedFile,
                    $expectedLinesCount,
                    self::numberFileLines($expectedLines, 'EXPECTED OUTPUT', $expectedFile),
                    self::numberFileLines($providedLines, 'PROVIDED OUTPUT', $commandFile),
                ])
            );

            for ($i = 0; $i < count($expectedLines); ++$i) {
                static::assertLineFuzzyEqualsLine($expectedLines[$i] ?? null, $providedLines[$i] ?? null, $i + 1, $expectedFile);
            }
        } else {
            static::assertStringEqualsFile($expectedFile, $generatedOut);
        }
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

    public static function createStyleInstance(InputInterface $i = null, OutputInterface $o = null): StyleInterface
    {
        return new Style($i ?: static::createInputInstance(), $o ?: static::createOutputInstance());
    }

    private static function shuffleArrayPreserveKeys(array $array): array
    {
        $keys = array_keys($array);
        shuffle($keys);

        return array_combine($keys, array_map(function ($k) use ($array) {
            return $array[$k];
        }, $keys));
    }

    /**
     * @param mixed $index
     */
    private function assertValidSingleChoiceAnswer(ChoiceAnswer $answer, string $question, array $choices, string $value, $index): void
    {
        $this->assertSame($question, $answer->stringifyQuestion());

        $this->assertInstanceOf(Question::class, $answer->getQuestion());
        $this->assertTrue($answer->hasAnswer());
        $this->assertFalse($answer->isMultiAnswer());
        $this->assertFalse($answer->isBooleanAnswer());
        $this->assertTrue($answer->isStringAnswer());

        $this->assertContains($answer->getAnswer(), $choices);
        $this->assertContains($answer->getIndex(), array_keys($choices));
        $this->assertSame($choices[$answer->getIndex()], $answer->getAnswer());
        $this->assertSame($value, $answer->getAnswer());
        $this->assertSame((string) $index, (string) $answer->getIndex());
        $this->assertSame(mb_strlen($value), $answer->length());
    }

    private function assertMultipleChoiceAnswerAccessors(MultipleChoiceAnswer $c, array $choices, array $inputs): void
    {
        $this->assertCount($c->count(), $inputs);

        foreach ($c->getAnswer() as $a) {
            $this->assertContains($a->getAnswer(), $inputs);
        }

        foreach ($inputs as $i) {
            $this->assertStringContainsString($i, $c->stringifyAnswer());
            $this->assertInstanceOf(AnswerInterface::class, $c->findAnswer($i));

            foreach ($c->filterAnswers($i) as $f) {
                $this->assertInstanceOf(AnswerInterface::class, $f);
            }
        }

        $this->assertSame($inputs[0], $c->firstAnswer()->stringifyAnswer());

        $c->filterAnswers(function ($a) {
            $this->assertInstanceOf(ChoiceAnswer::class, $a);
        });

        $this->assertCount(0, $c->filterAnswers(function ($a) {
            return false;
        }));

        $c->findAnswer(function ($a) {
            $this->assertInstanceOf(ChoiceAnswer::class, $a);
        });

        $this->assertNull($c->findAnswer(function ($a) {
            return false;
        }));
    }

    private function assertNonInteractiveBooleanAnswerAccessors(BooleanAnswer $answer, bool $expected): void
    {
        $this->assertSame($expected, $answer->getAnswer());
        $this->assertSame($expected ? 'true' : 'false', $answer->stringifyAnswer());
        $this->assertTrue($answer->hasAnswer());
        $this->assertTrue($answer->isBooleanAnswer());
        $this->assertFalse($answer->isMultiAnswer());
        $this->assertFalse($answer->isStringAnswer());
        $this->assertFalse($answer->isHidden());
        $this->assertSame($expected, $answer->getDefault());

        if ($expected) {
            $this->assertTrue($answer->isTrue());
            $this->assertFalse($answer->isFalse());
        } else {
            $this->assertFalse($answer->isTrue());
            $this->assertTrue($answer->isFalse());
        }
    }

    /**
     * @return string[]
     */
    private static function getCandies(): array
    {
        return [
            'a' => 'Air Heads',
            'g' => 'Gummy Bears',
            'r' => 'Reese\'s',
            't' => 'Twizzlers',
            's' => 'Skittles',
            'h' => 'Hershey\'s',
            'n' => 'Snickers',
            'j' => 'Jolly Rancher',
            'x' => 'Twix',
            '3' => '3 Musketeers',
            'b' => 'Starburst',
            'k' => 'KitKat',
            'f' => 'Fun Dip',
            'm' => 'M&Ms',
            'p' => 'Sour Patch Kids',
            'l' => 'Tootsie Roll',
        ];
    }

    /**
     * @param array $options
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

    private static function createInputInstance(): InputInterface
    {
        return new MemoryInput();
    }

    private static function createOutputInstance(): OutputInterface
    {
        return new TestOutput();
    }

    /**
     * @param string|string[] $contents
     */
    private static function numberFileLines($contents, string $fileHead = null, string $fileName = null): string
    {
        $lineNumb = 0;
        $contents = array_map(function (string $line) use (&$lineNumb): string {
            return sprintf('[%02d] "%s"', ++$lineNumb, $line);
        }, is_string($contents) ? explode("\n", $contents) : $contents);

        if (null === $fileHead && null !== $fileName) {
            $fileHead = $fileName;
            $fileName = null;
        }

        if (null !== $fileHead) {
            array_unshift($contents, '');
            array_unshift($contents, '---');
            if (null !== $fileName) {
                array_unshift($contents, sprintf('--- [%s] (%s)', $fileHead, $fileName));
            } else {
                array_unshift($contents, sprintf('--- [%s]', $fileHead));
            }
            array_unshift($contents, '---');
        }

        return implode("\n", $contents);
    }

    /**
     * @param string $expectedText
     * @param string $providedText
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
     */
    private static function assertGeneratedOutputEqualsExpectedRegExp($expectedRegExp, $providedOutput, int $fileLine, string $fileName)
    {
        static::assertMatchesRegularExpression($expectedRegExp, $providedOutput, vsprintf(
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
