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
use SR\Console\Tests\Output\TestOutput;
use SR\Reflection\Inspect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class StyleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Command
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $tester;

    /**
     * @param int $verbosity
     *
     * @return Style
     */
    private function getStyle($verbosity = OutputInterface::VERBOSITY_NORMAL)
    {
        $input = new ArrayInput([]);
        $output = new TestOutput();
        $output->setVerbosity($verbosity);
        $style = new Style($input, $output);

        return $style;
    }

    protected function setUp()
    {
        $this->command = new Command('sfstyle');
        $this->tester = new CommandTester($this->command);
    }

    protected function tearDown()
    {
        $this->command = null;
        $this->tester = null;
    }

    /**
     * @dataProvider inputCommandToOutputFilesProvider
     */
    public function testOutputs()
    {
        $base = __DIR__.'/../Fixtures/Style';
        $data = array_map(null, glob($base.'/command/command_*.php'), glob($base.'/output/output_*.txt'));

        for ($i = 0; $i < count($data); ++$i) {
            $this->handleOutputs($data[$i][0], $data[$i][1]);
        }
    }

    private function handleOutputs($inputCommandFilepath, $outputFilepath)
    {
        $code = require $inputCommandFilepath;
        $this->command->setCode($code);
        $this->tester->execute(array(), array('interactive' => false, 'decorated' => false));
        $file = file_get_contents($outputFilepath);
        $this->assertStringEqualsFile($outputFilepath, $this->tester->getDisplay(true), $inputCommandFilepath);
    }

    public function inputCommandToOutputFilesProvider()
    {
        $baseDir = __DIR__.'/../Fixtures/Style/SymfonyStyle';

        return array_map(null, glob($baseDir.'/command/command_*.php'), glob($baseDir.'/output/output_*.txt'));
    }

    public function testLongWordsBlockWrapping()
    {
        $word = 'Lopadotemachoselachogaleokranioleipsanodrimhypotrimmatosilphioparaomelitokatakechymenokichlepikossyphophattoperisteralektryonoptekephalliokigklopeleiolagoiosiraiobaphetraganopterygon';
        $wordLength = strlen($word);
        $style = $this->getStyle();
        $inspect = Inspect::this($style);
        $property = $inspect->getProperty('lineLengthMax');
        $maxLineLength = $property->value($style) - 3;

        $this->command->setCode(
            function (InputInterface $input, OutputInterface $output) use ($word) {
                $sfStyle = new StyleWithForcedLineLength($input, $output);
                $sfStyle->block($word, 'CUSTOM', 'fg=white;bg=blue', ' § ', false);
            }
        );

        $this->tester->execute(array(), array('interactive' => false, 'decorated' => false));
        $expectedCount = (int) ceil($wordLength / ($maxLineLength)) + (int) ($wordLength > $maxLineLength - 5);
        $this->assertSame($expectedCount, substr_count($this->tester->getDisplay(true), ' § '));
    }
}

/**
 * Use this class in tests to force the line length
 * and ensure a consistent output for expectations.
 */
class StyleWithForcedLineLength extends Style
{
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);

        $ref = new \ReflectionProperty(get_parent_class($this), 'lineLength');
        $ref->setAccessible(true);
        $ref->setValue($this, 120);
    }
}
