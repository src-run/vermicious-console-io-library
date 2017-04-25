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

use SR\Console\Output\Style\StyleAwareTrait;
use SR\Console\Output\Style\StyleInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

class TableHorizontalHelper
{
    use StyleAwareTrait;

    /**
     * @param StyleInterface $style
     */
    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
    }

    /**
     * @param array $headers
     * @param array ...$rows
     *
     * @return self
     */
    public function write(array $headers, ...$rows): self
    {
        list($headers, $rows) = $this->decorateData($headers, $rows);

        $table = new Table($this->io);
        $table = $this->stylizeTable($table);

        if (0 < count($headers)) {
            $table->setHeaders($headers);
        }

        if (0 < count($rows)) {
            $table->setRows($rows);
        }

        $this->io->prependBlock();
        $table->render();
        $this->io->newline();

        return $this;
    }

    /**
     * @param Table $table
     *
     * @return Table
     */
    private function stylizeTable(Table $table): Table
    {
        $decorator = new DecorationHelper('blue');

        $style = new TableStyle();
        $style->setVerticalBorderChar($decorator->decorate('|'));
        $style->setHorizontalBorderChar($decorator->decorate('-'));
        $style->setCrossingChar($decorator->decorate('+'));
        $style->setCellHeaderFormat('%s');
        $table->setStyle($style);

        return $table;
    }

    /**
     * @param array $headers
     * @param array $rows
     *
     * @return array
     */
    protected function decorateData(array $headers, array $rows): array
    {
        $headers = array_map(function ($header) {
            return (new DecorationHelper('blue', null, 'bold'))->decorate($header);
        }, $headers);

        return [$headers, $rows];
    }
}

/* EOF */
