<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Style;

trait StyleAwareExternalTrait
{
    /**
     * @var StyleInterface|null
     */
    protected $style;

    /**
     * @param StyleInterface|null $style
     */
    public function setStyle(StyleInterface $style = null)
    {
        $this->style = $style;
    }

    /**
     * @return StyleInterface|null
     */
    public function style(): ?StyleInterface
    {
        return $this->style;
    }
}
