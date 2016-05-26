<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SR\Console\Tests\Style;

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
     * @param string $what
     * @param mixed  ...$parameters
     *
     * @return mixed
     */
    public function execute($what, ...$parameters)
    {
        ob_start();

        $method = $this->inspect->getMethod($what);
        $method->invoke($this->style, ...$parameters);

        $this->display = ob_get_flush();

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
