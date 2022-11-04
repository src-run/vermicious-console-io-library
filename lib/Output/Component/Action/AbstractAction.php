<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Action;

use SR\Console\Output\Component\Action\Style\Extras\ExtrasText;
use SR\Console\Output\Component\Action\Style\Status\StatusProgress;
use SR\Console\Output\Component\Action\Style\Status\StatusText;
use SR\Console\Output\Exception\RuntimeException;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleAwareExternalTrait;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Output\Utility\State\State;

abstract class AbstractAction implements ActionStates
{
    use StyleAwareExternalTrait;

    /**
     * @var State
     */
    private $state;

    /**
     * @var \Closure
     */
    private $prefixFormatter;

    /**
     * @var Markup
     */
    private $prefixDefMarkup;

    /**
     * @var \Closure|null
     */
    private $actionFormatter;

    /**
     * @var Markup
     */
    private $actionDefMarkup;

    /**
     * @var \Closure
     */
    private $statusBeginFormatter;

    /**
     * @var Markup
     */
    private $statusBeginDefMarkup;

    /**
     * @var \Closure
     */
    private $statusInnerFormatter;

    /**
     * @var Markup
     */
    private $statusInnerDefMarkup;

    /**
     * @var \Closure
     */
    private $statusCloseFormatter;

    /**
     * @var Markup
     */
    private $statusCloseDefMarkup;

    /**
     * @var string
     */
    private $statusProgressCharacter;

    /**
     * @var \Closure
     */
    private $resultFormatter;

    /**
     * @var Markup
     */
    private $resultDefMarkup;

    /**
     * @var \Closure
     */
    private $extrasBeginFormatter;

    /**
     * @var Markup
     */
    private $extrasBeginDefMarkup;

    /**
     * @var \Closure
     */
    private $extrasInnerFormatter;

    /**
     * @var Markup
     */
    private $extrasInnerDefMarkup;

    /**
     * @var \Closure
     */
    private $extrasCloseFormatter;

    /**
     * @var Markup
     */
    private $extrasCloseDefMarkup;

    /**
     * @var bool
     */
    private $supportExtras;

    /**
     * @var int
     */
    private $finalNewlines;

    public function __construct(
        ?StyleInterface $style = null,
        ?Markup $prefixDefMarkup = null,
        ?\Closure $prefixFormatter = null,
        ?Markup $actionDefMarkup = null,
        ?\Closure $actionFormatter = null,
        ?Markup $statusBeginDefMarkup = null,
        ?\Closure $statusBeginFormatter = null,
        ?Markup $statusInnerDefMarkup = null,
        ?\Closure $statusInnerFormatter = null,
        ?string $statusProgressCharacter = null,
        ?Markup $statusCloseDefMarkup = null,
        ?\Closure $statusCloseFormatter = null,
        ?Markup $resultDefMarkup = null,
        ?\Closure $resultFormatter = null,
        ?Markup $extrasBeginDefMarkup = null,
        ?\Closure $extrasBeginFormatter = null,
        ?Markup $extrasInnerDefMarkup = null,
        ?\Closure $extrasInnerFormatter = null,
        ?Markup $extrasCloseDefMarkup = null,
        ?\Closure $extrasCloseFormatter = null
    ) {
        $this->state = new State(self::STATE_INACTIVE);
        $this->prefixDefMarkup = $prefixDefMarkup ?? Markup::createExplicit();
        $this->prefixFormatter = $prefixFormatter;
        $this->actionDefMarkup = $actionDefMarkup ?? Markup::createExplicit();
        $this->actionFormatter = $actionFormatter;
        $this->statusBeginDefMarkup = $statusBeginDefMarkup ?? Markup::createExplicit();
        $this->statusBeginFormatter = $statusBeginFormatter;
        $this->statusInnerDefMarkup = $statusInnerDefMarkup ?? Markup::createExplicit();
        $this->statusInnerFormatter = $statusInnerFormatter;
        $this->statusProgressCharacter = $statusProgressCharacter;
        $this->statusCloseDefMarkup = $statusCloseDefMarkup ?? Markup::createExplicit();
        $this->statusCloseFormatter = $statusCloseFormatter;
        $this->resultDefMarkup = $resultDefMarkup ?? Markup::createExplicit();
        $this->resultFormatter = $resultFormatter;
        $this->extrasBeginDefMarkup = $extrasBeginDefMarkup ?? Markup::createExplicit();
        $this->extrasBeginFormatter = $extrasBeginFormatter;
        $this->extrasInnerDefMarkup = $extrasInnerDefMarkup ?? Markup::createExplicit();
        $this->extrasInnerFormatter = $extrasInnerFormatter;
        $this->extrasCloseDefMarkup = $extrasCloseDefMarkup ?? Markup::createExplicit();
        $this->extrasCloseFormatter = $extrasCloseFormatter;

        $this
            ->setNewlinesCount()
            ->setSupportExtras()
            ->setStyle($style)
        ;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function setNewlinesCount(int $newlinesCount = null): self
    {
        $this->state->stateRequireRunAndSetAction(
            __METHOD__,
            function () use ($newlinesCount) {
                $this->finalNewlines = $newlinesCount ?? 2;
            },
            [
                self::STATE_INACTIVE,
                self::STATE_PREFIX,
                self::STATE_ACTION,
                self::STATE_STATUS_TEXT_ACTIVE,
                self::STATE_STATUS_TEXT_INACTIVE,
                self::STATE_STATUS_PROGRESS_ACTIVE,
                self::STATE_STATUS_PROGRESS_INACTIVE,
            ]
        );

        return $this;
    }

    public function setSupportExtras(bool $supportExtras = null): self
    {
        $this->state->stateRequireRunAndSetAction(
            __METHOD__,
            function () use ($supportExtras) {
                $this->supportExtras = $supportExtras ?? false;
            },
            [
                self::STATE_INACTIVE,
                self::STATE_PREFIX,
                self::STATE_ACTION,
                self::STATE_STATUS_TEXT_ACTIVE,
                self::STATE_STATUS_TEXT_INACTIVE,
                self::STATE_STATUS_PROGRESS_ACTIVE,
                self::STATE_STATUS_PROGRESS_INACTIVE,
            ]
        );

        return $this;
    }

    public function setStatusProgressCharacter(string $character): self
    {
        $this->state->stateRequireRunAndSetAction(
            __METHOD__,
            function () use ($character) {
                $this->statusProgressCharacter = $character;
            },
            [
                self::STATE_INACTIVE,
                self::STATE_PREFIX,
                self::STATE_ACTION,
                self::STATE_STATUS_TEXT_ACTIVE,
                self::STATE_STATUS_TEXT_INACTIVE,
                self::STATE_STATUS_PROGRESS_INACTIVE,
            ]
        );

        return $this;
    }

    /**
     * @param Markup $markup
     */
    public function prefix(string $prefix = null, Markup $markup = null): self
    {
        $this->state->stateRequireRunAndSetAction(
            __METHOD__,
            function () use ($prefix, $markup) {
                $this->style()->prependText()->write(
                    ($this->prefixFormatter)($markup ?? $this->prefixDefMarkup, $prefix ?? '')
                );
            },
            self::STATE_INACTIVE,
            self::STATE_PREFIX
        );

        return $this;
    }

    /**
     * @param Markup $markup
     */
    public function action(string $action, Markup $markup = null): self
    {
        if ($this->state->isState(self::STATE_INACTIVE)) {
            $this->prefix();
        }

        $this->state->stateRequireRunAndSetAction(
            __METHOD__,
            function () use ($action, $markup) {
                $this->style()->write(
                    ($this->actionFormatter)($markup ?? $this->actionDefMarkup, $action)
                );
            },
            self::STATE_PREFIX,
            self::STATE_ACTION
        );

        return $this;
    }

    public function statusText(string $status = null, bool $end = false): StatusText
    {
        $this->state->stateRequirements(__METHOD__, self::STATE_ACTION, self::STATE_STATUS_TEXT_ACTIVE);

        $s = new StatusText(
            $this->style(),
            $this,
            $this->statusBeginDefMarkup,
            $this->statusBeginFormatter,
            $this->statusInnerDefMarkup,
            $this->statusInnerFormatter,
            $this->statusCloseDefMarkup,
            $this->statusCloseFormatter
        );

        if (null !== $status) {
            $s->text($status);
        }

        return $s;
    }

    public function statusProgress(int $steps = null, int $progress = 0, string $character = null): StatusProgress
    {
        $p = new StatusProgress(
            $this->style(),
            $this,
            $this->statusBeginDefMarkup,
            $this->statusBeginFormatter,
            $this->statusInnerDefMarkup,
            $this->statusInnerFormatter,
            $this->statusCloseDefMarkup,
            $this->statusCloseFormatter,
            $steps,
            $character ?? $this->statusProgressCharacter
        );

        return 0 < $progress ? $p->progress($progress) : $p;
    }

    /**
     * @param Markup $markup
     */
    public function result(string $result, bool $supportExtras = null, Markup $markup = null): self
    {
        if (null !== $supportExtras) {
            $this->setSupportExtras($supportExtras);
        }

        $this->state->stateRequireRunAndSetAction(
            __METHOD__,
            function () use ($markup, $result) {
                $this->style()->write(
                    ($this->resultFormatter)($markup ?? $this->resultDefMarkup, $result)
                );
            },
            [
                self::STATE_ACTION,
                self::STATE_STATUS_TEXT_INACTIVE,
                self::STATE_STATUS_PROGRESS_INACTIVE,
            ],
            self::STATE_RESULT
        );

        if (false === $this->supportExtras) {
            $this->complete();
        }

        return $this;
    }

    abstract public function resultDone(string $result = null, bool $supportExtras = null): self;

    abstract public function resultOkay(string $result = null, bool $supportExtras = null): self;

    abstract public function resultWarn(string $result = null, bool $supportExtras = null): self;

    abstract public function resultStop(string $result = null, bool $supportExtras = null): self;

    abstract public function resultFail(string $result = null, bool $supportExtras = null): self;

    public function extras(string ...$extras): ExtrasText
    {
        if (false === $this->supportExtras) {
            throw new RuntimeException('Action extras disabled: enable by passing "true" to "extrasEnabled()"');
        }

        if ($this->state->isState(self::STATE_RESULT)) {
            $this->style()->write(' ');
        }

        $e = new ExtrasText(
            $this->style(),
            $this,
            $this->extrasBeginDefMarkup,
            $this->extrasBeginFormatter,
            $this->extrasInnerDefMarkup,
            $this->extrasInnerFormatter,
            $this->extrasCloseDefMarkup,
            $this->extrasCloseFormatter
        );

        foreach ($extras as $extra) {
            $e->text($extra);
        }

        return $e;
    }

    public function complete(): self
    {
        $this->state->stateRequireRunAndSetAction(
            __METHOD__,
            function () {
                $this->style()->newline($this->finalNewlines);
            },
            [
                self::STATE_ACTION,
                self::STATE_STATUS_TEXT_INACTIVE,
                self::STATE_STATUS_PROGRESS_INACTIVE,
                self::STATE_EXTRAS_TEXT_INACTIVE,
                self::STATE_RESULT,
                self::STATE_EXTRAS_TEXT_ACTIVE,
            ],
            self::STATE_INACTIVE
        );

        return $this;
    }
}
