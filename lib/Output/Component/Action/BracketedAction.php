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

use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleInterface;

final class BracketedAction extends AbstractAction
{
    /**
     * @param StyleInterface|null $style
     * @param Markup|null         $prefixDefMarkup
     * @param \Closure|null       $prefixFormatter
     * @param Markup|null         $actionDefMarkup
     * @param \Closure|null       $actionFormatter
     * @param Markup|null         $statusBeginDefMarkup
     * @param \Closure|null       $statusBeginFormatter
     * @param Markup|null         $statusInnerDefMarkup
     * @param \Closure|null       $statusInnerFormatter
     * @param string|null         $statusProgressCharacter
     * @param Markup|null         $statusCloseDefMarkup
     * @param \Closure|null       $statusCloseFormatter
     * @param Markup|null         $resultDefMarkup
     * @param \Closure|null       $resultFormatter
     * @param Markup|null         $extrasBeginDefMarkup
     * @param \Closure|null       $extrasBeginFormatter
     * @param Markup|null         $extrasInnerDefMarkup
     * @param \Closure|null       $extrasInnerFormatter
     * @param Markup|null         $extrasCloseDefMarkup
     * @param \Closure|null       $extrasCloseFormatter
     */
    public function __construct(
        ?StyleInterface $style = null,
        ?Markup         $prefixDefMarkup = null,
        ?\Closure       $prefixFormatter = null,
        ?Markup         $actionDefMarkup = null,
        ?\Closure       $actionFormatter = null,
        ?Markup         $statusBeginDefMarkup = null,
        ?\Closure       $statusBeginFormatter = null,
        ?Markup         $statusInnerDefMarkup = null,
        ?\Closure       $statusInnerFormatter = null,
        ?string         $statusProgressCharacter = null,
        ?Markup         $statusCloseDefMarkup = null,
        ?\Closure       $statusCloseFormatter = null,
        ?Markup         $resultDefMarkup = null,
        ?\Closure       $resultFormatter = null,
        ?Markup         $extrasBeginDefMarkup = null,
        ?\Closure       $extrasBeginFormatter = null,
        ?Markup         $extrasInnerDefMarkup = null,
        ?\Closure       $extrasInnerFormatter = null,
        ?Markup         $extrasCloseDefMarkup = null,
        ?\Closure       $extrasCloseFormatter = null
    ) {
        parent::__construct(
            $style,
            $prefixDefMarkup ?? Markup::createExplicit(),
            $prefixFormatter ?? function (Markup $markup, string $prefix): string {
                return $markup(sprintf('%s ', Markup::createExplicit()($prefix)));
            },
            $actionDefMarkup ?? Markup::createExplicit(),
            $actionFormatter ?? function (Markup $markup, string $action): string {
                return Markup::createExplicit()(sprintf('%s ... ', $markup(sprintf('[ %s ]', $action))));
            },
            $statusBeginDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, null, Markup::O_BOLD),
            $statusBeginFormatter ?? function (Markup $markup): string {
                return $markup('(');
            },
            $statusInnerDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, null, Markup::O_BOLD),
            $statusInnerFormatter ?? function (Markup $markup, string $character): string {
                return $markup($character);
            },
            $statusProgressCharacter,
            $statusCloseDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, null, Markup::O_BOLD),
            $statusCloseFormatter ?? function (Markup $markup): string {
                return $markup(')');
            },
            $resultDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, Markup::C_WHITE),
            $resultFormatter ?? function (Markup $markup, string $result): string {
                return $markup(sprintf(' %s ', mb_strtoupper($result)));
            },
            $extrasBeginDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, null, Markup::O_BOLD),
            $extrasBeginFormatter ?? function (Markup $markup): string {
                return $markup('(');
            },
            $extrasInnerDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, null, Markup::O_BOLD),
            $extrasInnerFormatter ?? function (Markup $markup, string $character): string {
                return $markup($character);
            },
            $extrasCloseDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, null, Markup::O_BOLD),
            $extrasCloseFormatter ?? function (Markup $markup): string {
                return $markup(')');
            }
        );
    }

    /**
     * @param string|null $result
     * @param bool|null   $supportExtras
     *
     * @return AbstractAction
     */
    public function resultDone(string $result = null, bool $supportExtras = null): AbstractAction
    {
        return $this->result($result ?? 'done', $supportExtras, new Markup(Markup::C_WHITE, Markup::C_BLACK));
    }

    /**
     * @param string|null $result
     * @param bool|null   $supportExtras
     *
     * @return AbstractAction
     */
    public function resultOkay(string $result = null, bool $supportExtras = null): AbstractAction
    {
        return $this->result($result ?? 'okay', $supportExtras, new Markup(Markup::C_BLACK, Markup::C_GREEN));
    }

    /**
     * @param string|null $result
     * @param bool|null   $supportExtras
     *
     * @return AbstractAction
     */
    public function resultWarn(string $result = null, bool $supportExtras = null): AbstractAction
    {
        return $this->result($result ?? 'warn', $supportExtras, new Markup(Markup::C_BLACK, Markup::C_YELLOW));
    }

    /**
     * @param string|null $result
     * @param bool|null   $supportExtras
     *
     * @return AbstractAction
     */
    public function resultStop(string $result = null, bool $supportExtras = null): AbstractAction
    {
        return $this->resultWarn($result ?? 'stop', $supportExtras);
    }

    /**
     * @param string|null $result
     * @param bool|null   $supportExtras
     *
     * @return AbstractAction
     */
    public function resultFail(string $result = null, bool $supportExtras = null): AbstractAction
    {
        return $this->result($result ?? 'fail', $supportExtras, new Markup(Markup::C_WHITE, Markup::C_RED, Markup::O_BOLD));
    }
}
