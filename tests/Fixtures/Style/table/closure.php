<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Component\Table\HorizontalTable;
use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->table([
        'header 1',
        'header 2',
    ], [
        'row 1a',
        'row 2a',
    ], [
        'row 1b',
        'row 2b',
    ]);

    $s->tableVertical([
        'header 1',
        'header 2',
    ], [
        'row 1a',
        'row 1b',
    ], [
        'row 2a',
        'row 2b',
    ]);

    $table = new HorizontalTable($s);
    $table->writeRows([
        'row 1a',
        'row 1b',
        'row 1c',
    ], [
        'row 2a',
        'row 2b',
        'row 2c',
    ], [
        'row 3a',
        'row 3b',
        'row 3c',
    ]);
};
