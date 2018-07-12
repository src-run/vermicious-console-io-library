<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Listing;

use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;

final class DefinitionList
{
    use StyleAwareInternalTrait;

    /**
     * @var \Closure
     */
    private $titleFormatter;

    /**
     * @var \Closure
     */
    private $definitionFormatter;

    /**
     * @var \Closure
     */
    private $lineFormatter;

    /**
     * @param StyleInterface $style
     * @param \Closure|null  $titleFormatter
     * @param \Closure|null  $definitionFormatter
     */
    public function __construct(StyleInterface $style, \Closure $titleFormatter = null, \Closure $definitionFormatter = null, \Closure $lineFormatter = null)
    {
        $this->setStyle($style);
        $this->setTitleFormatter($titleFormatter);
        $this->setDefinitionFormatter($definitionFormatter);
        $this->setLineFormatter($lineFormatter);
    }

    /**
     * @param \Closure|null $formatter
     *
     * @return self
     */
    public function setTitleFormatter(\Closure $formatter = null): self
    {
        $this->titleFormatter = $formatter ?? function (string $title, int $maxLength) {
            return (new Markup('black', null, 'bold'))->markupValue(
                sprintf('[%s]', $this->style()->pad($title, $maxLength, ' ', STR_PAD_RIGHT))
            );
        };

        return $this;
    }

    /**
     * @param \Closure|null $formatter
     *
     * @return self
     */
    public function setDefinitionFormatter(\Closure $formatter = null): self
    {
        $this->definitionFormatter = $formatter ?? function (string $definition, int $maxLength) {
            return (new Markup('white', null, 'bold'))->markupValue($definition);
        };

        return $this;
    }

    /**
     * @param \Closure|null $formatter
     *
     * @return self
     */
    public function setLineFormatter(\Closure $formatter = null): self
    {
        $this->lineFormatter = $formatter ?? function (string $title, string $definition) {
            return sprintf(' %s -> %s', $title, $definition);
        };

        return $this;
    }

    /**
     * @param array $definitions
     *
     * @return self
     */
    public function definitions(array $definitions): self
    {
        $this->style()->prependText();
        $this->writeDefinitionLines($definitions);
        $this->style()->newline();

        return $this;
    }

    /**
     * @param string[] $definitions
     *
     * @return self
     */
    private function writeDefinitionLines(array $definitions): self
    {
        $keyMaxLen = $this->getMaxArrayValueLength(array_keys($definitions));
        $valMaxLen = $this->getMaxArrayValueLength(array_values($definitions));

        array_walk($definitions, function (string &$definition, string $title) use ($keyMaxLen, $valMaxLen) {
            $definition = ($this->lineFormatter)(
                ($this->titleFormatter)($title, $keyMaxLen),
                ($this->definitionFormatter)($definition, $valMaxLen)
            );
        });

        $this->style()->writeln(array_values($definitions));

        return $this;
    }

    /**
     * @param string[] $array
     *
     * @return int
     */
    private function getMaxArrayValueLength(array $array): int
    {
        return max(array_map(function ($a) {
            return mb_strlen($a);
        }, $array));
    }
}
