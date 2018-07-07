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

class HorizontalTable extends AbstractTable
{
    /**
     * @param array[] ...$rows
     *
     * @return self|AbstractTable
     */
    public function writeRows(...$rows): AbstractTable
    {
        return $this->write([], ...$rows);
    }

    /**
     * @param string[] $h
     * @param array[]  $r
     *
     * @return array[]
     */
    protected function formatInputData(array $h, array $r): array
    {
        return [$h, $r];
    }
}
