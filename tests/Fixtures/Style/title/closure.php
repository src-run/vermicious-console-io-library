<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Style\Style;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Tests\Fixtures\ApplicationWithProps;
use SR\Console\Tests\Fixtures\ApplicationWithoutProps;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 120);

    $s->title('A title string');

    $s->setVerbosity(StyleInterface::VERBOSITY_NORMAL);
    $s->applicationTitle(new ApplicationWithProps());

    $s->setVerbosity(StyleInterface::VERBOSITY_VERY_VERBOSE);
    $s->applicationTitle(new ApplicationWithProps());

    $s->setVerbosity(StyleInterface::VERBOSITY_NORMAL);
    $s->applicationTitle(new ApplicationWithoutProps());
};
