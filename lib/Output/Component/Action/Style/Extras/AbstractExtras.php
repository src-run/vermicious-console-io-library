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
use SR\Console\Output\Component\Action\Style\ActionStyleTrait;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleInterface;

abstract class AbstractExtras
{
    use ActionStyleTrait;

    public function __construct(
        StyleInterface $style,
        AbstractAction $action,
        Markup $beginDefMarkup,
        \Closure $beginFormatter,
        Markup $innerDefMarkup,
        \Closure $innerFormatter,
        Markup $afterDefMarkup,
        \Closure $afterFormatter
    ) {
        $this->setStyle($style);
        $this
            ->setAction($action)
            ->setBeginDefMarkup($beginDefMarkup)
            ->setBeginFormatter($beginFormatter)
            ->setInnerDefMarkup($innerDefMarkup)
            ->setInnerFormatter($innerFormatter)
            ->setAfterDefMarkup($afterDefMarkup)
            ->setAfterFormatter($afterFormatter)
        ;
    }

    /**
     * @return self|ExtrasText
     */
    abstract public function start(Markup $markup = null): self;

    /**
     * @param Markup|null $markup
     */
    abstract public function finish(Markup $markup): AbstractAction;
}
