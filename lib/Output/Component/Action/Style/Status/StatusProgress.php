<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Action\Style\Status;

use SR\Console\Output\Component\Action\AbstractAction;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleInterface;

final class StatusProgress extends AbstractStatus
{
    /**
     * @var string
     */
    private $character;

    /**
     * @var int|null
     */
    private $stepsCount;

    /**
     * @var int
     */
    private $stepActive = 0;

    /**
     * @var bool
     */
    private $autoFinish = true;

    /**
     * @param string $character
     */
    public function __construct(
        StyleInterface $style,
        AbstractAction $action,
        Markup $beginDefMarkup,
        \Closure $beginFormatter,
        Markup $innerDefMarkup,
        \Closure $innerFormatter,
        Markup $afterDefMarkup,
        \Closure $afterFormatter,
        ?int $steps,
        ?string $character
    ) {
        parent::__construct(
            $style,
            $action,
            $beginDefMarkup,
            $beginFormatter,
            $innerDefMarkup,
            $innerFormatter,
            $afterDefMarkup,
            $afterFormatter
        );

        $this
            ->setCharacter($character ?? '.')
            ->setStepsCount($steps)
        ;
    }

    public function isInactive(): bool
    {
        return false === $this->getAction()->getState()->isState(AbstractAction::STATE_STATUS_PROGRESS_ACTIVE);
    }

    public function setCharacter(string $character): self
    {
        $this->getAction()->getState()->stateInverseRequireRunAndSetAction(
            __METHOD__,
            function () use ($character) {
                $this->character = $character;
            },
            AbstractAction::STATE_STATUS_PROGRESS_ACTIVE
        );

        return $this;
    }

    public function setStepsCount(int $steps = null): self
    {
        $this->getAction()->getState()->stateInverseRequireRunAndSetAction(
            __METHOD__,
            function () use ($steps) {
                $this->stepsCount = $steps;
            },
            AbstractAction::STATE_STATUS_PROGRESS_ACTIVE
        );

        return $this;
    }

    public function setAutoFinish(bool $autoFinish): self
    {
        $this->autoFinish = $autoFinish;

        return $this;
    }

    public function isCompleted(): bool
    {
        return null !== $this->stepsCount && $this->stepActive >= $this->stepsCount;
    }

    /**
     * @return self|AbstractStatus
     */
    public function start(Markup $markup = null): AbstractStatus
    {
        $this->getAction()->getState()->stateRequireRunAndSetAction(
            __METHOD__,
            function () use ($markup) {
                $this->style()->write(($this->beginFormatter)($markup ?? $this->beginDefMarkup));
            },
            [
                AbstractAction::STATE_ACTION,
                AbstractAction::STATE_STATUS_TEXT_INACTIVE,
                AbstractAction::STATE_STATUS_PROGRESS_INACTIVE,
            ],
            AbstractAction::STATE_STATUS_PROGRESS_ACTIVE
        );

        return $this;
    }

    public function progress(int $count = 1, string $character = null, Markup $markup = null): self
    {
        $this->getAction()->getState()->stateConditionalSetRunAction(
            __METHOD__,
            function () {
                $this->start();
            },
            [
                AbstractAction::STATE_ACTION,
                AbstractAction::STATE_STATUS_TEXT_INACTIVE,
                AbstractAction::STATE_STATUS_PROGRESS_INACTIVE,
            ],
            AbstractAction::STATE_STATUS_PROGRESS_ACTIVE
        );

        for ($i = 0; $i < $count; ++$i) {
            $this->getAction()->getState()->stateRequireRunAndSetAction(
                __METHOD__,
                function () use ($character, $markup) {
                    $this->style()->write(
                        ($this->innerFormatter)($markup ?? $this->innerDefMarkup, $character ?? $this->character)
                    );
                },
                AbstractAction::STATE_STATUS_PROGRESS_ACTIVE
            );
            ++$this->stepActive;

            if (true === $this->autoFinish && true === $this->isCompleted()) {
                $this->finish();
            }
        }

        return $this;
    }

    public function finish(Markup $markup = null): AbstractAction
    {
        $this->getAction()->getState()->stateConditionalSetRunAction(
            __METHOD__,
            function () use ($markup) {
                $this->style()
                    ->write(($this->afterFormatter)($markup ?? $this->afterDefMarkup))
                    ->write(' ')
                ;
            },
            AbstractAction::STATE_STATUS_PROGRESS_ACTIVE,
            AbstractAction::STATE_STATUS_PROGRESS_INACTIVE
        );

        $this->getAction()->getState()->setState(AbstractAction::STATE_STATUS_PROGRESS_INACTIVE);

        return $this->getAction();
    }
}
