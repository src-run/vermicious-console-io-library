<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Utility\Interpolate;

use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Reflection\Inspect;

trait StringInterpolatorTrait
{
    /**
     * @var string
     */
    private static $interpolatorType = PsrStringInterpolator::class;

    /**
     * @var bool
     */
    private static $interpolatorThrows = false;

    /**
     * @param string $interpolator
     */
    public static function setInterpolatorType(string $interpolator): void
    {
        try {
            $inspect = Inspect::useClass($interpolator);
        } catch (\Exception $exception) {
            throw new InvalidArgumentException(
                'Invalid string interpolator type "%s": %s', $interpolator, $exception->getMessage(), $exception
            );
        }

        if (!$inspect->extendsClass(AbstractStringInterpolator::class)) {
            throw new InvalidArgumentException(
                'Invalid string interpolator type "%s": does not extend "%s" parent class', $interpolator, AbstractStringInterpolator::class
            );
        }

        self::$interpolatorType = $interpolator;
    }

    /**
     * @param bool $throws
     */
    public static function setInterpolatorThrows(bool $throws): void
    {
        self::$interpolatorThrows = $throws;
    }

    /**
     * @param string[]|string $lines
     * @param mixed[]         $replacements
     *
     * @return array|string
     */
    private static function interpolate($lines, $replacements = [])
    {
        if (is_string($lines) || null === $lines) {
            return self::interpolateLine($lines ?? '', $replacements);
        }

        return array_map(function (string $line) use ($replacements) {
            return self::interpolateLine($line, $replacements);
        }, $lines);
    }

    /**
     * @param string  $line
     * @param mixed[] $replacements
     *
     * @return string
     */
    private static function interpolateLine(string $line, array $replacements = []): string
    {
        return self::createInterpolator($line, $replacements)->compile(self::$interpolatorThrows);
    }

    /**
     * @param string  $format
     * @param mixed[] $replacements
     *
     * @return AbstractStringInterpolator
     */
    private static function createInterpolator(string $format, array $replacements): AbstractStringInterpolator
    {
        return new self::$interpolatorType($format, $replacements);
    }
}
