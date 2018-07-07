<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Component\Listing\SimpleList;
use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $list = [
        'item 1',
        'item 2',
        'item 3',
        'item 4',
        'item 5',
    ];

    $s->listing($list);

    $h = new SimpleList($s);
    $h->listing($list);

    foreach ($list as $line) {
        $h->line($line);
    }

    $h->listingClose();

    foreach ($list as $line) {
        $h->line($line, true);
    }
};
