<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Input;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Interface InputAwareInterface.
 */
interface InputAwareInterface
{
    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input);

    /**
     * @return InputInterface
     */
    public function getInput();
}

/* EOF */
