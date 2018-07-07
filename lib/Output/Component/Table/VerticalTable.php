<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Table;

use SR\Exception\Logic\InvalidArgumentException;

class VerticalTable extends AbstractTable
{
    /**
     * @param string[] $h
     * @param array[]  $r
     *
     * @return array[]
     */
    protected function formatInputData(array $h, array $r): array
    {
        if (count($r) !== count($h)) {
            throw new InvalidArgumentException('Header count does not match row count!');
        }

        foreach (static::stylizeHeaders($h) as $i => $v) {
            array_unshift($r[$i], $v);
        }

        return [[], $r];
    }
}
