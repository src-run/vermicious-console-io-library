<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Style;

use SR\Console\Input\Component\Question\QuestionHelper;
use SR\Console\Output\Component\Action\AbstractAction;
use SR\Console\Output\Component\Block\Block;
use SR\Console\Output\Component\Header\SectionHeader;
use SR\Console\Output\Component\Header\TitleHeader;
use SR\Console\Output\Component\Listing\DefinitionList;
use SR\Console\Output\Component\Listing\SimpleList;
use SR\Console\Output\Component\Progress\AbstractProgressHelper;
use SR\Console\Output\Component\Table\AbstractTable;
use SR\Console\Output\Component\Text\Text;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait StyleAwareInternalTrait
{
    /**
     * @var StyleInterface|null
     */
    protected $style;

    /**
     * @param StyleInterface|null $style
     */
    protected function setStyle(StyleInterface $style = null)
    {
        $this->style = $style;
    }

    /**
     * @return StyleInterface
     */
    protected function style(): ?StyleInterface
    {
        return $this->style;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return QuestionHelper|AbstractAction|SectionHeader|TitleHeader|DefinitionList|SimpleList|AbstractProgressHelper|AbstractTable|Block|Text
     */
    protected function createStyleNewInputOutput(InputInterface $input, OutputInterface $output): self
    {
        if ($this->style->getInput() === $input && $this->style->getOutput() === $output) {
            return $this;
        }

        return new static(new Style($input, $output));
    }
}
