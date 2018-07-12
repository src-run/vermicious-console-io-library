<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\StreamOutput;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @param null|resource|string[] $stream
     * @param bool                   $interactive
     *
     * @return StreamableInputInterface|MockObject
     */
    protected function createInputStream($stream = null, bool $interactive = true): StreamableInputInterface
    {
        if (is_array($stream)) {
            $stream = $this->createInputStreamResource(...$stream);
        }

        if (null !== $stream && !is_resource($stream)) {
            $this->fail('Valid resource must be provided to create input stream.');
        }

        $input = $this
            ->getMockBuilder(StreamableInputInterface::class)
            ->getMock();

        $input
            ->expects($this->any())
            ->method('isInteractive')
            ->will($this->returnValue($interactive));

        if ($stream) {
            $input
                ->expects($this->any())
                ->method('getStream')
                ->willReturn($stream);
        }

        return $input;
    }

    /**
     * @param string ...$lines
     *
     * @return resource
     */
    protected function createInputStreamResource(string ...$lines)
    {
        $stream = fopen('php://memory', 'r+', false);

        foreach ($lines as $l) {
            fwrite($stream, $l.PHP_EOL);
        }

        rewind($stream);

        if (!is_resource($stream)) {
            $this->fail(sprintf('Failed to create input stream with lines: %s', implode(', ', array_map(function (string $line): string {
                return sprintf('"%s"', $line);
            }, $lines))));
        }

        return $stream;
    }

    /**
     * @return StreamOutput
     */
    protected function createOutputStream(): StreamOutput
    {
        return new StreamOutput(fopen('php://memory', 'r+', false));
    }

    private function hasSttyAvailable()
    {
        exec('stty 2>&1', $output, $exitcode);

        return 0 === $exitcode;
    }
}
