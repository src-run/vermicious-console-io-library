<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Style;

use SR\Console\Input\InputAwareTrait;
use SR\Console\Output\OutputAwareTrait;

/**
 * Trait StyleAwareTrait.
 */
trait StyleAwareTrait // implements StyleAwareInterface
{
    use InputAwareTrait;
    use OutputAwareTrait;

    /**
     * @var StyleInterface
     */
    protected $style;

    /**
     * @param StyleInterface $style
     */
    public function setStyle(StyleInterface $style)
    {
        $this->style = $style;
    }

    /**
     * @return StyleInterface
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param \Closure|null $closure
     * @param mixed ...$parameters
     *
     * @return mixed|StyleInterface
     */
    protected function io(\Closure $closure = null, ...$parameters)
    {
        if ($closure !== null) {
            return $this->ioInvoke($closure, null, ...$parameters);
        }

        return $this->getStyle();
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioQuiet(\Closure $closure)
    {
        if (!$this->style->isQuiet()) {
            return null;
        }

        return $this->ioInvoke($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioNormal(\Closure $closure)
    {
        if (!$this->style->isNormal()) {
            return null;
        }

        return $this->ioInvoke($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioNotVerbose(\Closure $closure)
    {
        if (!$this->style->isNormal() && !$this->style->isQuiet()) {
            return null;
        }

        return $this->ioInvoke($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioVerbose(\Closure $closure)
    {
        if (!$this->style->isVerbose()) {
            return null;
        }

        return $this->ioInvoke($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioVeryVerbose(\Closure $closure)
    {
        if (!$this->style->isVeryVerbose()) {
            return null;
        }

        return $this->ioInvoke($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioDebug(\Closure $closure)
    {
        if (!$this->style->isDebug()) {
            return null;
        }

        return $this->ioInvoke($closure);
    }

    /**
     * @param \Closure    $closure
     * @param null|object $bindTo
     * @param mixed ...$parameters
     *
     * @return mixed
     */
    protected function ioInvoke(\Closure $closure, $bindTo = null, ...$parameters)
    {
        if ($bindTo !== null) {
            $closure = $closure->bindTo($bindTo);
        }

        return $closure($this->style, ...$parameters);
    }
}

/* EOF */
