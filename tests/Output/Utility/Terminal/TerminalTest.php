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
use SR\Console\Output\Utility\Terminal\Terminal;

/**
 * @covers \SR\Console\Output\Utility\Terminal\Terminal
 */
class TerminalTest extends TestCase
{
    public function testY(): void
    {
        $y = Terminal::y();

        $this->assertInternalType('int', $y);
        $this->assertGreaterThan(0, $y);
        $this->assertSame($y, Terminal::height());
    }

    public function testX(): void
    {
        $x = Terminal::x();

        $this->assertInternalType('int', $x);
        $this->assertGreaterThan(0, $x);
        $this->assertSame($x, Terminal::width());
    }

    public function testStty(): void
    {
        $this->assertTrue(Terminal::stty());
    }

    public function testShell(): void
    {
        $p = [
            '/bin/zsh',
            '/usr/bin/zsh',
            '/bin/ksh',
            '/usr/bin/ksh',
            '/bin/csh',
            '/usr/bin/csh',
            '/bin/bash',
            '/usr/bin/bash',
            '/bin/sh',
            '/usr/bin/sh',
        ];

        $s = Terminal::shell();

        $this->assertNotNull($s);
        $this->assertContains($s, $p);

        try {
            $r = new \ReflectionClass(Terminal::class);
        } catch (\ReflectionException $e) {
            $this->fail(sprintf('Failed to create reflection for "%s"', Terminal::class));
        }

        $m = $r->getMethod('resolveShellFromEnvGuessing');
        $m->setAccessible(true);
        $s = $m->invoke(null);

        $this->assertNotNull($s);
        $this->assertContains($s, $p);
    }

    public function testIsShell(): void
    {
        $s = Terminal::shell();

        $this->assertNotNull($s);
        $this->assertTrue(Terminal::isShell($s));
        $this->assertTrue(Terminal::isShell(basename($s)));
        $this->assertFalse(Terminal::isShell('foobar'));
        $this->assertFalse(Terminal::isShell('/usr/bin/foobar'));
    }

    public static function provideLocateData(): \Generator
    {
        $executables = [
            'bash' => '/bin/bash',
            'bunzip2' => '/bin/bunzip2',
            'busybox' => '/bin/busybox',
            'bzcat' => '/bin/bzcat',
            'bzdiff' => '/bin/bzdiff',
            'bzexe' => '/bin/bzexe',
            'bzgrep' => '/bin/bzgrep',
            'bzip2' => '/bin/bzip2',
            'bzmore' => '/bin/bzmore',
            'cat' => '/bin/cat',
            'chacl' => '/bin/chacl',
            'chgrp' => '/bin/chgrp',
            'chmod' => '/bin/chmod',
            'chown' => '/bin/chown',
            'cp' => '/bin/cp',
            'date' => '/bin/date',
            'dd' => '/bin/dd',
            'df' => '/bin/df',
            'dir' => '/bin/dir',
            'dmesg' => '/bin/dmesg',
            'dumpkeys' => '/bin/dumpkeys',
            'echo' => '/bin/echo',
            'egrep' => '/bin/egrep',
            'fgrep' => '/bin/fgrep',
            'getfacl' => '/bin/getfacl',
            'grep' => '/bin/grep',
            'gunzip' => '/bin/gunzip',
            'gzexe' => '/bin/gzexe',
            'gzip' => '/bin/gzip',
            'hostname' => '/bin/hostname',
            'ip' => '/bin/ip',
            'kill' => '/bin/kill',
            'less' => '/bin/less',
            'lessecho' => '/bin/lessecho',
            'lesskey' => '/bin/lesskey',
            'lesspipe' => '/bin/lesspipe',
            'ln' => '/bin/ln',
            'ls' => '/bin/ls',
            'lsblk' => '/bin/lsblk',
            'mkdir' => '/bin/mkdir',
            'more' => '/bin/more',
            'mount' => '/bin/mount',
            'mv' => '/bin/mv',
            'nano' => '/bin/nano',
            'netstat' => '/bin/netstat',
            'ping' => '/bin/ping',
            'ps' => '/bin/ps',
            'pwd' => '/bin/pwd',
            'readlink' => '/bin/readlink',
            'rm' => '/bin/rm',
            'rmdir' => '/bin/rmdir',
            'sed' => '/bin/sed',
            'sleep' => '/bin/sleep',
            'stty' => '/bin/stty',
            'su' => '/bin/su',
            'tar' => '/bin/tar',
            'touch' => '/bin/touch',
            'umount' => '/bin/umount',
            'uname' => '/bin/uname',
            'which' => '/bin/which',
            'zcat' => '/bin/zcat',
            'zcmp' => '/bin/zcmp',
            'zdiff' => '/bin/zdiff',
            'zgrep' => '/bin/zgrep',
            'zsh' => '/bin/zsh',
            'ksh' => '/usr/bin/ksh',
            'csh' => '/usr/bin/csh',
            'vi' => '/usr/bin/vi',
            'php' => '/usr/bin/php',
        ];

        foreach ($executables as $name => $path) {
            if (file_exists($path)) {
                yield [$name, $path];
            }
        }
    }

    /**
     * @dataProvider provideLocateData
     *
     * @param string $name
     * @param string $path
     */
    public function testLocate(string $name, string $path): void
    {
        $this->assertContains($path, Terminal::locateAll($name));
    }
}
