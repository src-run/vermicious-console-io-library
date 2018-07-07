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
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Tests\Fixtures\ApplicationWithoutProps;
use SR\Console\Tests\Fixtures\ApplicationWithProps;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->title('A title string');

    $s->setVerbosity(StyleInterface::VERBOSITY_NORMAL);
    $s->applicationTitle(new ApplicationWithProps());

    $s->setVerbosity(StyleInterface::VERBOSITY_VERY_VERBOSE);
    $s->applicationTitle(new ApplicationWithProps());

    $s->setVerbosity(StyleInterface::VERBOSITY_NORMAL);
    $s->applicationTitle(new ApplicationWithoutProps());
};
