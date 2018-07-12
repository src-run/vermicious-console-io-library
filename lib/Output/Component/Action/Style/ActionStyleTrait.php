<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Action\Style;

use SR\Console\Output\Component\Action\AbstractAction;
use SR\Console\Output\Component\Action\Style\Extras\ExtrasText;
use SR\Console\Output\Component\Action\Style\Status\StatusText;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleAwareExternalTrait;

trait ActionStyleTrait
{
    use StyleAwareExternalTrait;

    /**
     * @var Markup
     */
    protected $beginDefMarkup;

    /**
     * @var \Closure
     */
    protected $beginFormatter;

    /**
     * @var Markup
     */
    protected $innerDefMarkup;

    /**
     * @var \Closure
     */
    protected $innerFormatter;

    /**
     * @var Markup
     */
    protected $afterDefMarkup;

    /**
     * @var \Closure
     */
    protected $afterFormatter;

    /**
     * @var AbstractAction
     */
    protected $action;

    /**
     * @return AbstractAction
     */
    public function getAction(): AbstractAction
    {
        return $this->action;
    }

    /**
     * @param Markup $beginDefMarkup
     *
     * @return self|ExtrasText|StatusText
     */
    public function setBeginDefMarkup(Markup $beginDefMarkup): self
    {
        $this->beginDefMarkup = $beginDefMarkup;

        return $this;
    }

    /**
     * @param \Closure $beginFormatter
     *
     * @return self|ExtrasText|StatusText
     */
    public function setBeginFormatter(\Closure $beginFormatter): self
    {
        $this->beginFormatter = $beginFormatter;

        return $this;
    }

    /**
     * @param Markup $innerDefMarkup
     *
     * @return self|ExtrasText|StatusText
     */
    public function setInnerDefMarkup(Markup $innerDefMarkup): self
    {
        $this->innerDefMarkup = $innerDefMarkup;

        return $this;
    }

    /**
     * @param \Closure $innerFormatter
     *
     * @return self|ExtrasText|StatusText
     */
    public function setInnerFormatter(\Closure $innerFormatter): self
    {
        $this->innerFormatter = $innerFormatter;

        return $this;
    }

    /**
     * @param Markup $afterDefMarkup
     *
     * @return self|ExtrasText|StatusText
     */
    public function setAfterDefMarkup(Markup $afterDefMarkup): self
    {
        $this->afterDefMarkup = $afterDefMarkup;

        return $this;
    }

    /**
     * @param \Closure $afterFormatter
     *
     * @return self|ExtrasText|StatusText
     */
    public function setAfterFormatter(\Closure $afterFormatter): self
    {
        $this->afterFormatter = $afterFormatter;

        return $this;
    }

    /**
     * @param AbstractAction $action
     *
     * @return self||ExtrasText
     */
    protected function setAction(AbstractAction $action): self
    {
        $this->action = $action;

        return $this;
    }
}
