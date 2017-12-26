<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;
use SR\Console\Tests\Input\TestInput;

return function (InputInterface $input, OutputInterface $output) {
    $i = new TestInput();
    $i->setInput(['y', 'n', 'rmf', 'orange', 0]);
    $s = new Style($i, $output, 80);

    \PHPUnit_Framework_TestCase::assertTrue(
        $s->confirm('A boolean question with yes or no answer')
    );
    \PHPUnit_Framework_TestCase::assertFalse(
        $s->confirm('A boolean question with yes or no answer')
    );
    \PHPUnit_Framework_TestCase::assertEquals(
        'rmf',
        $s->ask('Enter your initials')
    );
    \PHPUnit_Framework_TestCase::assertEquals(
        'orange',
        $s->choice('Select your preferred fruit', ['apple', 'orange'])
    );
    \PHPUnit_Framework_TestCase::assertEquals(
        'apple',
        $s->choice('Select your preferred fruit', ['apple', 'orange'])
    );
};
