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
     * @var SymfonyTerminal
     */
    private static $terminal;

    /**
     * @var bool
     */
    private static $stty;

    /**
     * @var string|null
     */
    private static $shell;

    /**
     * Get the terminal width or return the default if unresolvable.
     *
     * @param int|null $default
     *
     * @return int|null
     */
    public static function x(int $default = null): ?int
    {
        return self::terminal()->getWidth() ?: $default;
    }

    /**
     * Get the terminal height or return the default if unresolvable.
     *
     * @param int|null $default
     *
     * @return int|null
     */
    public static function y(int $default = null): ?int
    {
        return self::terminal()->getHeight() ?: $default;
    }

    /**
     * Get the terminal width or return the default if unresolvable.
     *
     * @param int|null $default
     *
     * @return int|null
     */
    public static function width(int $default = null): ?int
    {
        return self::x($default);
    }

    /**
     * Get the terminal height or return the default if unresolvable.
     *
     * @param int|null $default
     *
     * @return int|null
     */
    public static function height(int $default = null): ?int
    {
        return self::y($default);
    }

    /**
     * Determines whether set teletype (stty) is available.
     *
     * @return bool
     */
    public static function stty(): bool
    {
        if (null === self::$stty) {
            self::$stty = self::resolveStty();
        }

        return self::$stty;
    }

    /**
     * Determines the shell type available.
     *
     * @return string|null
     */
    public static function shell(): ?string
    {
        if (null === self::$shell) {
            self::$shell = self::resolveShellFromEnvVariable() ?? self::resolveShellFromEnvGuessing();
        }

        return self::$shell;
    }

    /**
     * Checks if passed shell is active.
     *
     * @return bool
     */
    public static function isShell(string $name): bool
    {
        return self::shell() === $name || basename(self::shell()) === $name;
    }

    /**
     * Attempts to locate an absolute command path from name.
     *
     * @param string $executable
     *
     * @return null|string
     */
    public static function locate(string $executable): ?string
    {
        return (null !== $o = self::locateAll($executable)) ? array_shift($o) : null;
    }

    /**
     * Attempts to locate all absolute command paths from name.
     *
     * @param string $executable
     *
     * @return null|string[]
     */
    public static function locateAll(string $executable): ?array
    {
        exec(sprintf('which -a %s 2> /dev/null', $executable), $output, $return);

        return 0 === $return && count($output) > 0 ? self::sanitizeOutput($output) : null;
    }

    /**
     * @param string $env
     *
     * @return null|string
     */
    private static function resolveShellFromEnvVariable(): ?string
    {
        $s = null;

        if (null !== $env = self::locateEnv()) {
            exec(sprintf('%s 2> /dev/null', $env), $output, $return);

            if (0 === $return && 0 < count($output)) {
                $output = array_filter($output, function (string $line): bool {
                    return 0 === mb_strpos($line, 'SHELL=');
                });

                if (1 === count($output) && null !== $o = self::shiftAndSanitizeOutput($output)) {
                    $s = self::locate($o = explode('=', $o)[1]) ?? $o;
                }
            }
        }

        return $s;
    }

    /**
     * @return null|string
     */
    private static function resolveShellFromEnvGuessing(): ?string
    {
        $s = null;

        if (null !== $env = self::locateEnv()) {
            foreach (['zsh', 'bash', 'ksh', 'csh', 'sh'] as $name) {
                if ('OK' === self::sanitizeOutput(shell_exec(sprintf('%s %s -c \'echo OK\' 2> /dev/null', $env, $name)))) {
                    $s = self::locate($name) ?? $name;
                    break;
                }
            }
        }

        return $s;
    }

    /**
     * @return null|string
     */
    private static function locateEnv(): ?string
    {
        $env = '/usr/bin/env';

        return (file_exists($env) || null !== $env = self::locate('env')) ? $env : null;
    }

    /**
     * @return bool
     */
    private static function resolveStty(): bool
    {
        exec('stty 2>&1', $output, $return);

        return 0 === $return || null !== self::locate('stty');
    }

    /**
     * @param string|string[] $output
     *
     * @return null|string
     */
    private static function shiftAndSanitizeOutput($output): ?string
    {
        return self::sanitizeOutput(is_array($output) ? array_shift($output) : $output);
    }

    /**
     * @param string|string[]$output
     *
     * @return string|string[]
     */
    private static function sanitizeOutput($output)
    {
        $sanitize = array_map(function (?string $line): ?string {
            return trim($line);
        }, (array) $output);

        return is_array($output) ? $sanitize : array_shift($sanitize);
    }

    /**
     * @return SymfonyTerminal
     */
    private static function terminal(): SymfonyTerminal
    {
        if (null === self::$terminal) {
            self::$terminal = new SymfonyTerminal();
        }

        return self::$terminal;
    }
}
