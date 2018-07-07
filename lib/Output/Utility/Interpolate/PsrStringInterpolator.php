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

final class PsrStringInterpolator extends AbstractStringInterpolator
{
    /**
     * @return string
     */
    protected function interpolate(): string
    {
        $message = $this->format;

        foreach ($this->getNormalizedReplacements() as $search => $replace) {
            $message = str_replace(sprintf('{%s}', $search), $replace, $message);
        }

        return $message;
    }
}
