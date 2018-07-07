<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Console\Output\Style\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->definitions([
        'definition name 1' => 'value 1',
        'definition name 2' => 'value 2',
        'definition name 3' => 'value 3',
        'definition name 4' => 'value 4',
        'alternate definition name 5' => 'value 5',
    ]);
};
