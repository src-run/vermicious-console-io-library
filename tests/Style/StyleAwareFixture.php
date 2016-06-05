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

use SR\Console\Style\StyleAwareTrait;
use SR\Console\Style\StyleInterface;

/**
 * Class StyleAwareTest.
 */
class StyleAwareFixture
{
    use StyleAwareTrait;

    /**
     * @return int
     */
    public function getVerbosity()
    {
        return $this->style->getOutput()->getVerbosity();
    }

    /**
     * @param \Closure|null $closure
     * @param mixed ...$parameters
     *
     * @return mixed|StyleInterface
     */
    public function styleIo(\Closure $closure = null, ...$parameters)
    {
        return $this->io($closure, $parameters);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    public function styleIoQuiet(\Closure $closure)
    {
        return $this->ioQuiet($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    public function styleIoNoVerbose(\Closure $closure)
    {
        return $this->ioNoVerbose($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    public function styleIoVerbose(\Closure $closure)
    {
        return $this->ioVerbose($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    public function styleIoVeryVerbose(\Closure $closure)
    {
        return $this->ioVeryVerbose($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    public function styleIoDebug(\Closure $closure)
    {
        return $this->ioDebug($closure);
    }

    /**
     * @param \Closure    $closure
     * @param null|object $bindTo
     * @param mixed       ...$parameters
     *
     * @return mixed
     */
    public function styleIoInvoke(\Closure $closure, $bindTo = null, ...$parameters)
    {
        return $this->ioInvoke($closure, $bindTo, $parameters);
    }
}

/* EOF */
