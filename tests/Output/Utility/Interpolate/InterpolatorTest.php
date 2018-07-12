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
use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Console\Output\Exception\RuntimeException;
use SR\Console\Output\Utility\Interpolate\PsrStringInterpolator;
use SR\Console\Output\Utility\Interpolate\PsrStringInterpolatorTrait;

/**
 * @covers \SR\Console\Output\Utility\Interpolate\AbstractStringInterpolator
 * @covers \SR\Console\Output\Utility\Interpolate\PsrStringInterpolator
 * @covers \SR\Console\Output\Utility\Interpolate\PsrStringInterpolatorTrait
 */
class InterpolatorTest extends TestCase
{
    public function testAddReplacements(): void
    {
        $instance = new PsrStringInterpolator('one: {one} / two: {two} / three: {three}', [
            'three' => 3,
        ]);
        $instance->addReplacements([
            'one' => 1,
        ]);
        $instance->addOneReplacement(2, 'two');

        $this->assertSame('one: 1 / two: 2 / three: 3', $instance->compile());
    }

    public function testOverwriteReplacements(): void
    {
        $instance = new PsrStringInterpolator('one: {one} / two: {two} / three: {three}', [
            'three' => 3,
        ]);
        $instance->addReplacements([
            'one' => 1,
            'two' => 2,
        ]);
        $instance->addOneReplacement(6, 'three', true);
        $instance->addOneReplacement(4, 'two', true);

        $this->assertSame('one: 1 / two: 4 / three: 6', $instance->compile());

        $this->expectException(InvalidArgumentException::class);

        $instance->addOneReplacement(100, 'two');
    }

    public function testIntIndexedReplacements(): void
    {
        $instance = new PsrStringInterpolator('one: {0} / two: {1} / three: {2}', [
            2,
            1,
        ]);
        $instance->addOneReplacement(0);

        $this->assertSame('one: 2 / two: 1 / three: 0', $instance->compile());
    }

    public function testInterpolatorTraitThrows(): void
    {
        $instance = new InterpolatorAware();
        $instance::setInterpolateThrows(false);
        $instance::setInterpolateNormalizer(function () {
            throw new RuntimeException();
        });
        $instance->callInterpolate(['line 1', 'line {line}'], [
            'line' => 2,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('throwing enabled');

        $instance = new InterpolatorAware();
        $instance::setInterpolateThrows(true);
        $instance::setInterpolateNormalizer(function () {
            throw new RuntimeException('throwing enabled');
        });
        $instance->callInterpolate(['line 1', 'line {line}'], [
            'line' => 2,
        ]);
    }
}

class InterpolatorAware
{
    use PsrStringInterpolatorTrait;

    /**
     * @param string[]|string $lines
     * @param mixed[]         $replacements
     *
     * @return array|string
     */
    public function callInterpolate($lines, $replacements = [])
    {
        return $this->interpolate($lines, $replacements);
    }
}
