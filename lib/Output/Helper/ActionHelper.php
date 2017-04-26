<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper;

use SR\Console\Output\Helper\DecorationHelper;
use SR\Console\Output\Style\StyleAwareTrait;
use SR\Console\Output\Style\StyleInterface;

class ActionHelper
{
    use StyleAwareTrait;

    /**
     * @param StyleInterface $style
     */
    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
    }

    /**
     * @param string $action
     */
    public function action(string $action)
    {
        $this->io->prependText();
        $this->io->write(sprintf(' [ %s ] ... ', $action));
    }

    /**
     * @param string $result
     * @param string $fg
     * @param string $bg
     * @param array ...$options
     *
     * @return self
     */
    public function actionResult(string $result, string $fg, string $bg, ...$options): self
    {
        $this->io->writeln((new DecorationHelper($fg, $bg, ...$options))->decorate(sprintF(' %s ', strtoupper($result))));
        $this->io->newline();

        return $this;
    }
}
