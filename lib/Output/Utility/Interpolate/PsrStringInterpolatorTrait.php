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

trait PsrStringInterpolatorTrait
{
    /**
     * @var bool
     */
    private static $interpolateThrows = false;

    /**
     * @var \Closure|null
     */
    private static $interpolateNormalizer;

    /**
     * @param bool $interpolateThrows
     */
    public static function setInterpolateThrows(bool $interpolateThrows): void
    {
        static::$interpolateThrows = $interpolateThrows;
    }

    /**
     * @param null|\Closure $interpolateNormalizer
     */
    public static function setInterpolateNormalizer(?\Closure $interpolateNormalizer): void
    {
        static::$interpolateNormalizer = $interpolateNormalizer;
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
        return self::createInterpolator($line, $replacements)->compile(static::$interpolateThrows);
    }

    /**
     * @param string  $format
     * @param mixed[] $replacements
     *
     * @return AbstractStringInterpolator
     */
    private static function createInterpolator(string $format, array $replacements): AbstractStringInterpolator
    {
        return new PsrStringInterpolator($format, $replacements, self::$interpolateNormalizer);
    }
}
