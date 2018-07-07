<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use SR\Console\Output\Style\Style;
use SR\Console\Tests\Input\MemoryInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $i = new MemoryInput();
    $i->setInput(['y', 'n', 'rmf', 'orange', 0]);
    $s = new Style($i, $output, 80);

    TestCase::assertTrue($s->confirm('A boolean question with yes or no answer')->getAnswer());
    TestCase::assertFalse($s->confirm('A boolean question with yes or no answer')->getAnswer());
    TestCase::assertEquals('rmf', $s->ask('Enter your initials'));
    TestCase::assertEquals('orange', $s->choice('Select your preferred fruit', ['apple', 'orange']));
    TestCase::assertEquals('apple', $s->choice('Select your favorite fruit', ['apple', 'orange']));
};
