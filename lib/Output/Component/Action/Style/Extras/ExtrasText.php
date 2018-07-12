<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Action\Style\Extras;

use SR\Console\Output\Component\Action\AbstractAction;
use SR\Console\Output\Markup\Markup;

final class ExtrasText extends AbstractExtras
{
    /**
     * @return bool
     */
    public function isInactive(): bool
    {
        return false === $this->getAction()->getState()->isState(AbstractAction::STATE_EXTRAS_TEXT_ACTIVE);
    }

    /**
     * @param Markup|null $markup
     *
     * @return self|AbstractExtras
     */
    public function start(Markup $markup = null): AbstractExtras
    {
        return $this;
    }

    /**
     * @param string      $text
     * @param Markup|null $markup
     *
     * @return self
     */
    public function text(string $text, Markup $markup = null): self
    {
        if ($this->getAction()->getState()->isState(AbstractAction::STATE_EXTRAS_TEXT_ACTIVE)) {
            $this->style()->write(' ');
        }

        $this->getAction()->getState()->stateConditionalSetRunAction(
            __METHOD__,
            function () {
                $this->start();
            },
            [
                AbstractAction::STATE_ACTION,
                AbstractAction::STATE_STATUS_TEXT_INACTIVE,
                AbstractAction::STATE_STATUS_PROGRESS_INACTIVE,
                AbstractAction::STATE_EXTRAS_TEXT_INACTIVE,
                AbstractAction::STATE_RESULT,
            ],
            AbstractAction::STATE_EXTRAS_TEXT_ACTIVE
        );

        $this->getAction()->getState()->stateRequireRunAndSetAction(
            __METHOD__,
            function () use ($text, $markup) {
                $this->style()
                    ->write(($this->beginFormatter)($markup ?? $this->beginDefMarkup))
                    ->write(($this->innerFormatter)($markup ?? $this->innerDefMarkup, $text))
                    ->write(($this->afterFormatter)($markup ?? $this->afterDefMarkup));
            },
            AbstractAction::STATE_EXTRAS_TEXT_ACTIVE
        );

        return $this;
    }

    /**
     * @param Markup|null $markup
     *
     * @return AbstractAction
     */
    public function finish(Markup $markup = null): AbstractAction
    {
        $this->getAction()->getState()->stateConditionalSetRunAction(
            __METHOD__,
            function () use ($markup) {},
            AbstractAction::STATE_EXTRAS_TEXT_ACTIVE,
            AbstractAction::STATE_EXTRAS_TEXT_INACTIVE
        );

        $this->getAction()->getState()->setState(AbstractAction::STATE_EXTRAS_TEXT_INACTIVE);

        return $this->getAction();
    }
}
