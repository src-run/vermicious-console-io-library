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
        $this->input = $style->getInput();
        $this->output = $style->getOutput();
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
     * @param mixed         ...$parameters
     *
     * @return mixed|StyleInterface
     */
    protected function io(\Closure $closure = null, ...$parameters)
    {
        if ($closure !== null) {
            return $this->ioInvoke($closure, ...$parameters);
        }

        return $this->getStyle();
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioQ(\Closure $closure)
    {
        if (!$this->io()->isQuiet()) {
            return $this->ioInvoke($closure);
        }
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioN(\Closure $closure)
    {
        if (!$this->io()->isVerbose() && !$this->io()->isVeryVerbose()) {
            return $this->ioInvoke($closure);
        }
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioV(\Closure $closure)
    {
        if ($this->io()->isVerbose()) {
            return $this->ioInvoke($closure);
        }
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioVV(\Closure $closure)
    {
        if ($this->io()->isVeryVerbose()) {
            return $this->ioInvoke($closure);
        }
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioVVV(\Closure $closure)
    {
        if ($this->io()->isDebug()) {
            return $this->ioInvoke($closure);
        }
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function ioClosure(\Closure $closure, ...$parameters)
    {
        return $this->ioInvoke($closure, null, ...$parameters);
    }

    /**
     * @param \Closure $closure
     * @param mixed    ...$parameters
     *
     * @return mixed
     */
    private function ioInvoke(\Closure $closure, $bindTo = null, ...$parameters)
    {
        if ($bindTo !== null) {
            $closure = $closure->bindTo($bindTo);
        }

        return $closure($this->style, ...$parameters);
    }
}

/* EOF */
