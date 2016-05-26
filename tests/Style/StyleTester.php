<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) 2016 Rob Frawley 2nd(rmf) <rmf AT src DOT run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Style;

use SR\Console\Style\Style;
use SR\Console\Style\StyleInterface;
use SR\Reflection\Inspect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StyleTester.
 */
class StyleTester
{
    /**
     * @var StyleInterface
     */
    private $style;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var \SR\Reflection\Introspection\ObjectIntrospection
     */
    private $inspect;

    /**
     * @var string
     */
    private $display;

    /**
     * @param mixed $style
     */
    public function __construct($style)
    {
        $this->style = $style;
        $this->input = $style->getInput();
        $this->output = $style->getOutput();
        $this->inspect = Inspect::thisInstance($style);
    }

    /**
     * @return mixed|StyleInterface|StyleAwareFixture
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string $what
     * @param mixed  ...$parameters
     *
     * @return mixed
     */
    public function execute($what, ...$parameters)
    {
        if ($this->style instanceof Style) {
            ob_start();
        }

        $method = $this->inspect->getMethod($what);
        $return = $method->invoke($this->style, ...$parameters);

        if (count($parameters) === 0) {
            if ($this->style instanceof Style) {
                ob_end_clean();
            }
            return $return;
        }

        if ($this->style instanceof StyleAwareFixture) {
            $this->display = $this->style->getOutput()->output;
            $this->style->getOutput()->clear();
        } elseif ($this->style instanceof Style) {
            $this->display = ob_get_flush();
            ob_end_clean();
        }

        return $this->display;
    }

    /**
     * Gets the display returned by the last execution of the command.
     *
     * @param bool $normalize Whether to normalize end of lines to \n or not
     *
     * @return string The display
     */
    public function getDisplay($normalize = false)
    {
        return $this->display;
    }
}

/* EOF */
