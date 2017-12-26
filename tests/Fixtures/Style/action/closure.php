<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Helper\Action\AltActionHelper;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->action('Peforming an action');
    $s->actionResult('custom', 'white', 'magenta', 'bold', 'reverse');

    $s->action('Performing a second action');
    $s->actionDone();

    $s->action('Performing a third action');
    $s->actionOkay();

    $s->action('Performing a fourth action');
    $s->actionStop();

    $s->action('Performing a fifth action');
    $s->actionFail();

    $a = new AltActionHelper($s);
    $a->setNewlinesAtFinish(0);

    $a->action('Doing something')->result('OK');
    $a->action('Another something')->result('ERR');
};
