<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Output\Component\Progress;

use PHPUnit\Framework\TestCase;
use SR\Console\Input\Component\Question\QuestionHelper;
use SR\Console\Tests\Input\MemoryInput;
use SR\Console\Tests\Style\StyleTest;

/**
 * @covers \SR\Console\Input\Component\Question\Answer\AbstractAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\BooleanAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\ChoiceAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\MultipleChoiceAnswer
 * @covers \SR\Console\Input\Component\Question\Answer\ScalarAnswer
 * @covers \SR\Console\Input\Component\Question\QuestionHelper
 */
class QuestionHelperTest extends TestCase
{
    public function testInteractiveConfirm()
    {
        $input = new MemoryInput();
        $input->setInput(['y', 'n']);
        $quest = new QuestionHelper($s = StyleTest::createStyleInstance($input));

        $result = $quest->confirm('A question', true);
        $this->assertTrue($result->getAnswer());
        $result = $quest->confirm('A question', true);
        $this->assertFalse($result->getAnswer());
    }
}
