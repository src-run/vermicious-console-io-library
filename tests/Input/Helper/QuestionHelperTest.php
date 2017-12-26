<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) 2016 Rob Frawley 2nd(rmf) <rmf AT src DOT run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Output\Helper\Progress;

use SR\Console\Input\Helper\QuestionHelper;
use SR\Console\Tests\Input\TestInput;
use SR\Console\Tests\Style\StyleTest;

class QuestionHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testInteractiveConfirm()
    {
        $input = new TestInput();
        $input->setInput(['y', 'n']);
        $quest = new QuestionHelper($s = StyleTest::createStyleInstance($input));

        $result = $quest->confirm('A question', true);
        $this->assertTrue($result);
        $result = $quest->confirm('A question', true);
        $this->assertFalse($result);
    }
}
