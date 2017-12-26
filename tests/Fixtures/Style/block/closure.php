<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SR\Console\Output\Helper\Text\BlockHelper;
use SR\Console\Output\Style\Style;

return function (InputInterface $input, OutputInterface $output) {
    $s = new Style($input, $output, 80);

    $s->info('Info block');
    $s->success('Success block');
    $s->warning('Warning block');
    $s->error('Error block');
    $s->critical('Critical block');

    foreach ([BlockHelper::TYPE_SM, BlockHelper::TYPE_MD, BlockHelper::TYPE_LG] as $type) {
        $s->block([
            'You call yourself a free spirit, a \'wild thing,\' and you\'re terrified somebody\'s gonna stick you in a cage. Well baby, you\'re already in that cage. You built it yourself. And it\'s not bounded in the west by Tulip, Texas, or in the east by Somali-land. It\'s wherever you go. Because no matter where you run, you just end up running into yourself.'
        ], 'Truman Capote (Breakfast at Tiffany\'s)', $type);

        $s->block('If this typewriter can’t do it, then f@#$ it, it can’t be done. ', 'Tom Robbins (Still Life with Woodpecker)', $type);

        $s->info('Info block', $type);
        $s->success('Success block', $type);
        $s->warning('Warning block', $type);
        $s->error('Error block', $type);
        $s->critical('Critical block', $type);
    }
};
