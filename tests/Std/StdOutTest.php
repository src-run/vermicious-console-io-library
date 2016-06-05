<?php

/*
 * This file is part of the `vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Std;

use SR\Console\Std\StdErr;
use SR\Console\Std\StdOut;
use SR\Console\Tests\Output\TestOutput;
use Symfony\Component\Yaml\Yaml;

/**
 * Class StdOutTest.
 */
class StdOutTest extends \PHPUnit_Framework_TestCase
{
    public function testStdOutAndStdErr()
    {
        $base = __DIR__.'/../Fixtures/Std';
        $data = array_map(null, glob($base.'/command/command_*.yml'), glob($base.'/output/output_*.txt'));

        for ($i = 0; $i < count($data); ++$i) {
            $instructions = Yaml::parse(file_get_contents($data[$i][0]));
            $this->handleTestStdOutAndStdErr($instructions, $data[$i][1]);
        }
    }

    /**
     * @param array  $instructions
     * @param string $outputFile
     */
    private function handleTestStdOutAndStdErr(array $instructions, $outputFile)
    {
        $class = $instructions['callable']['class'];
        $method = $instructions['callable']['method'];
        $params = $instructions['callable']['params'];
        $output = new TestOutput();

        $instance = new $class($output);
        $instance->{$method}(...$params);

        $this->assertStringEqualsFile($outputFile, $output->getOutput(), sprintf('%s->%s(%s)', $class, $method, (string) $params[0]));
    }

    public function testStdOutStream()
    {
        $out = new StdOut();
        $stream = $out->getDefaultOutputStream()->getStream();

        $this->assertSame(get_resource_type($stream), 'stream');
    }

    public function testStdErrStream()
    {
        $out = new StdErr();
        $stream = $out->getDefaultOutputStream()->getStream();

        $this->assertSame(get_resource_type($stream), 'stream');
    }
}

/* EOF */
