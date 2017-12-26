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

/**
 * @deprecated Deprecated in favor of two new traits that explicitly expose the functionality publicly or internally.
 *             {@see SR\Console\Output\Style\StyleAwareInternalTrait} and
 *             {@see SR\Console\Output\Style\StyleAwareExternalTrait}
 */
trait StyleAwareTrait
{
    use StyleAwareExternalTrait {
        setStyle as private overriddenSetStyle;
        style as getStyle;
    }

    /**
     * @var StyleInterface|null
     */
    protected $io;

    /**
     * @param StyleInterface|null $style
     */
    public function setStyle(StyleInterface $style = null)
    {
        $this->io = $style;

        $this->overriddenSetStyle($style);
    }
}
