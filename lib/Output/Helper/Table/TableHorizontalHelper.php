<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper\Table;

class TableHorizontalHelper extends AbstractTableHelper
{
    /**
     * @param array[] ...$rows
     *
     * @return self|AbstractTableHelper
     */
    public function writeRows(...$rows): AbstractTableHelper
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
