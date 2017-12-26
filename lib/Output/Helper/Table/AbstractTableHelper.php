<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper\Table;

use SR\Console\Output\Helper\Style\DecorationHelper;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

abstract class AbstractTableHelper
{
    use StyleAwareInternalTrait;

    /**
     * @param StyleInterface $style
     */
    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
    }

    /**
     * @param string[] $headers
     * @param array[]  ...$rows
     *
     * @return self
     */
    public function write(array $headers, ...$rows): self
    {
        $this->style->prependBlock();

        $table = $this->createTable(...$this->formatInputData($headers, $rows));
        $table->render();

        $this->style->newline();

        return $this;
    }

    /**
     * @param array   $h
     * @param array[] $r
     *
     * @return Table
     */
    private function createTable(array $h, array $r): Table
    {
        $color = new DecorationHelper('blue');
        $style = new TableStyle();

        $style->setVerticalBorderChar($color->decorate('|'));
        $style->setHorizontalBorderChar($color->decorate('-'));
        $style->setCrossingChar($color->decorate('+'));
        $style->setCellHeaderFormat('%s');

        $table = new Table($this->style);
        $table->setStyle($style);
        $table->setHeaders(static::stylizeHeaders($h));
        $table->setRows($r);

        return $table;
    }

    /**
     * @param string[] $headers
     *
     * @return string[]
     */
    protected static function stylizeHeaders(array $headers): array
    {
        $color = new DecorationHelper('blue', null, 'bold');

        return array_map(function ($h) use ($color) {
            return $color->decorate($h);
        }, $headers);
    }

    /**
     * @param string[] $h
     * @param array[]  $r
     *
     * @return array[]
     */
    abstract protected function formatInputData(array $h, array $r): array;
}
