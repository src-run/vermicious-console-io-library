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

final class SimpleAction extends AbstractAction
{
    /**
     * @param StyleInterface|null $style
     * @param Markup|null         $actionDefMarkup
     * @param \Closure|null       $actionFormatter
     * @param Markup|null         $prefixDefMarkup
     * @param \Closure|null       $prefixFormatter
     * @param Markup|null         $resultDefMarkup
     * @param \Closure|null       $resultFormatter
     * @param Markup|null         $extrasDefMarkup
     * @param \Closure|null       $extrasFormatter
     */
    public function __construct(
        StyleInterface $style = null,
        Markup         $actionDefMarkup = null,
        ?\Closure      $actionFormatter = null,
        Markup         $prefixDefMarkup = null,
        ?\Closure      $prefixFormatter = null,
        Markup         $resultDefMarkup = null,
        ?\Closure      $resultFormatter = null,
        Markup         $extrasDefMarkup = null,
        ?\Closure      $extrasFormatter = null
    ) {
        parent::__construct(
            $style,
            $actionDefMarkup ?? Markup::createExplicit(),
            $actionFormatter ?? function (Markup $markup, string $action) {
                return $markup(sprintf('%s ... ', $action));
            },
            $prefixDefMarkup ?? Markup::createExplicit('white', null, Markup::O_BOLD),
            $prefixFormatter ?? function (Markup $markup, string $prefix): string {
                return $markup(sprintf('(%s) ', $prefix));
            },
            $resultDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, Markup::C_WHITE),
            $resultFormatter ?? function (Markup $markup, string $result) {
                return $markup('[')
                      .(clone $markup)->addOptions(Markup::O_BOLD)(mb_strtolower($result))
                      .$markup(']');
            },
            $extrasDefMarkup ?? Markup::createExplicit(Markup::C_BLACK, null, Markup::O_BOLD),
            $extrasFormatter ?? function (Markup $markup, string ...$extras): string {
                return $markup(implode(' ', array_map(function ($extra) use ($markup) {
                    return sprintf('(%s)', $extra);
                }, $extras)));
            }
        );
    }

    /**
     * @param string $result
     *
     * @return AbstractAction
     */
    public function resultDone(string $result = 'done'): AbstractAction
    {
        return $this->result($result, new Markup(Markup::C_BLUE, null));
    }

    /**
     * @param string $result
     *
     * @return AbstractAction
     */
    public function resultOkay(string $result = 'okay'): AbstractAction
    {
        return $this->result($result, new Markup(Markup::C_GREEN, null));
    }

    /**
     * @param string $result
     *
     * @return AbstractAction
     */
    public function resultWarn(string $result = 'warn'): AbstractAction
    {
        return $this->result($result, new Markup(Markup::C_YELLOW, null));
    }

    /**
     * @param string $result
     *
     * @return AbstractAction
     */
    public function resultStop(string $result = 'stop'): AbstractAction
    {
        return $this->resultWarn($result);
    }

    /**
     * @param string $result
     *
     * @return AbstractAction
     */
    public function resultFail(string $result = 'fail'): AbstractAction
    {
        return $this->result($result, new Markup(Markup::C_RED, Markup::C_BLACK, Markup::O_BOLD, Markup::O_REVERSE));
    }
}
