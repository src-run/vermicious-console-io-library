<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Utility\Interpolate;

use SR\Exception\Runtime\RuntimeException;

final class SpfStringInterpolator extends AbstractStringInterpolator
{
    /**
     * @return string
     */
    protected function interpolate(): string
    {
        if (0 === count($replacements = $this->getNormalizedReplacements())) {
            return $this->format;
        }

        if (false === $compiled = @vsprintf($this->format, $replacements)) {
            throw new RuntimeException('Unable to interpolate "%s" with %d replacements', $this->format, count($this->replacements));
        }

        return $compiled;
    }
}
