<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Table;

use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

abstract class AbstractTable
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
        $this->style()->prependBlock();

        $table = $this->createTable(...$this->formatInputData($headers, $rows));
        $table->render();

        $this->style()->newline();

        return $this;
    }

    /**
     * @param string[] $headers
     *
     * @return string[]
     */
    protected static function stylizeHeaders(array $headers): array
    {
        return (new Markup('blue', null, 'bold'))->markupLines($headers);
    }

    /**
     * @param string[] $h
     * @param array[]  $r
     *
     * @return array[]
     */
    abstract protected function formatInputData(array $h, array $r): array;

    /**
     * @param array   $h
     * @param array[] $r
     *
     * @return Table
     */
    private function createTable(array $h, array $r): Table
    {
        $color = new Markup('blue');
        $style = new TableStyle();
        $style->setCellHeaderFormat('%s');
        $style->{method_exists($style, 'setVerticalBorderChars') ? 'setVerticalBorderChars' : 'setVerticalBorderChar'}(
            $color->markupValue('|')
        );
        $style->{method_exists($style, 'setHorizontalBorderChars') ? 'setHorizontalBorderChars' : 'setHorizontalBorderChar'}(
            $color->markupValue('-')
        );
        $style->{method_exists($style, 'setDefaultCrossingChar') ? 'setDefaultCrossingChar' : 'setCrossingChar'}(
            $color->markupValue('+')
        );

        $table = new Table($this->style());
        $table->setStyle($style);
        $table->setHeaders(static::stylizeHeaders($h));
        $table->setRows($r);

        return $table;
    }
}
