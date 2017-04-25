<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper;

use SR\Exception\Logic\InvalidArgumentException;

class TableVerticalHelper extends TableHorizontalHelper
{
    /**
     * @param array $headers
     * @param array $rows
     *
     * @return array
     */
    protected function decorateData(array $headers, array $rows): array
    {
        if (count($rows) !== count($headers)) {
            throw new InvalidArgumentException('Header count does not match row count!');
        }

        list($headers, $rows) = parent::decorateData($headers, $rows);

        foreach ($headers as $i => $h) {
            array_unshift($rows[$i], $h);
        }

        return [[], $rows];
    }
}

/* EOF */
