<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Utility\Terminal;

use Symfony\Component\Console\Terminal as SymfonyTerminal;

final class Terminal
{
    /**
     * @param int|null $default
     *
     * @return int|null
     */
    public static function x(int $default = null): ?int
    {
        return self::terminal()->getWidth() ?: $default;
    }

    /**
     * @return int|null
     */
    public static function columns(): ?int
    {
        return self::x();
    }

    /**
     * @return int|null
     */
    public static function width(): ?int
    {
        return self::x();
    }

    /**
     * @param int|null $default
     *
     * @return int|null
     */
    public static function y(int $default = null): ?int
    {
        return self::terminal()->getHeight() ?: $default;
    }

    /**
     * @return int|null
     */
    public static function rows(): ?int
    {
        return self::y();
    }

    /**
     * @return int|null
     */
    public static function height(): ?int
    {
        return self::y();
    }

    /**
     * @return SymfonyTerminal
     */
    private static function terminal(): SymfonyTerminal
    {
        static $terminal;

        if (null === $terminal) {
            $terminal = new SymfonyTerminal();
        }

        return $terminal;
    }
}
