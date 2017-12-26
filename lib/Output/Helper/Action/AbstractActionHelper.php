<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper\Action;

use SR\Console\Output\Helper\Style\DecorationHelper;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;

abstract class AbstractActionHelper
{
    use StyleAwareInternalTrait;

    /**
     * @var \Closure
     */
    private $actionFormatter;

    /**
     * @var \Closure
     */
    private $resultFormatter;

    /**
     * @var int
     */
    private $newlinesAtFinish = 1;

    /**
     * @param StyleInterface $style
     * @param \Closure|null  $actionFormatter
     * @param \Closure|null  $resultFormatter
     */
    public function __construct(StyleInterface $style, \Closure $actionFormatter = null, \Closure $resultFormatter = null)
    {
        $this->setStyle($style);
        $this->setActionFormatter($actionFormatter);
        $this->setResultFormatter($resultFormatter);
    }

    /**
     * @param \Closure|null $formatter
     *
     * @return self
     */
    public function setActionFormatter(\Closure $formatter = null): self
    {
        $this->actionFormatter = $formatter ?? function (string $action, DecorationHelper $decorator = null) {
            return sprintf(' %s ... ', ($decorator ?? new DecorationHelper())->decorate($action));
        };

        return $this;
    }

    /**
     * @param \Closure|null $formatter
     *
     * @return self
     */
    public function setResultFormatter(\Closure $formatter = null): self
    {
        $this->resultFormatter = $formatter ?? function (string $result, DecorationHelper $decorator = null) {
            return ($decorator ?? new DecorationHelper('black', 'white'))->decorate(strtoupper($result));
        };

        return $this;
    }

    /**
     * @param int $newlines
     *
     * @return self
     */
    public function setNewlinesAtFinish(int $newlines): self
    {
        $this->newlinesAtFinish = $newlines;

        return $this;
    }

    /**
     * @param string                $action
     * @param DecorationHelper|null $decorator
     *
     * @return self
     */
    public function action(string $action, DecorationHelper $decorator = null): self
    {
        $this->style->prependText();
        $this->style->write(($this->actionFormatter)($action, $decorator));

        return $this;
    }

    /**
     * @param string                $result
     * @param DecorationHelper|null $decorator
     *
     * @return self
     */
    public function result(string $result, DecorationHelper $decorator = null): self
    {
        $this->style->writeln(($this->resultFormatter)($result, $decorator));
        $this->style->newline($this->newlinesAtFinish);

        return $this;
    }
}
