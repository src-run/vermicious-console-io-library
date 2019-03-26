<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Utility\Interpolate;

use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Dumper\VarDumper\ReturnedCliDumper;

abstract class AbstractStringInterpolator
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var array
     */
    protected $replacements;

    /**
     * @var \Closure|null
     */
    private $normalizer;

    /**
     * @param string        $format
     * @param array         $replacements
     * @param \Closure|null $normalizer
     */
    public function __construct(string $format, array $replacements = [], ?\Closure $normalizer = null)
    {
        $this->setFormat($format);
        $this->setReplacements($replacements);
        $this->setNormalizer($normalizer);
    }

    /**
     * @param string $format
     *
     * @return self
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param array $replacements
     *
     * @return self
     */
    public function setReplacements(array $replacements): self
    {
        $this->replacements = $replacements;

        return $this;
    }

    /**
     * @param array $replacements
     * @param bool  $overwrite
     *
     * @return self
     */
    public function addReplacements(array $replacements, bool $overwrite = false): self
    {
        foreach ($replacements as $index => $value) {
            $this->addOneReplacement($value, $index, $overwrite);
        }

        return $this;
    }

    /**
     * @param string     $value
     * @param mixed|null $index
     * @param bool       $overwrite
     *
     * @return self
     */
    public function addOneReplacement(string $value, $index = null, bool $overwrite = false): self
    {
        if (null === $index || is_int($index)) {
            $this->replacements[] = $value;

            return $this;
        }

        if (!$overwrite && isset($this->replacements[$index])) {
            throw new InvalidArgumentException('Replacement with index "%s" cannot be overwritten', $index);
        }

        $this->replacements[$index] = $value;

        return $this;
    }

    /**
     * @param \Closure|null $normalizer
     *
     * @return self
     */
    public function setNormalizer(?\Closure $normalizer = null): self
    {
        $this->normalizer = $normalizer ?? function (string $string): string {
            return $string;
        };

        return $this;
    }

    /**
     * @param bool $throwOnFailure
     *
     * @return string
     */
    public function compile(bool $throwOnFailure = false): string
    {
        try {
            return ($this->normalizer)($this->interpolate());
        } catch (\RuntimeException $exception) {
            if ($throwOnFailure) {
                throw $exception;
            }
        }

        return $this->format;
    }

    /**
     * @return mixed[]
     */
    protected function getNormalizedReplacements(): array
    {
        return array_map(function ($value): string {
            return $this->normalizeReplacement($value);
        }, $this->replacements);
    }

    /**
     * @return string
     */
    abstract protected function interpolate(): string;

    /**
     * @param mixed $replacement
     *
     * @return string|int|float
     */
    private function normalizeReplacement($replacement)
    {
        if (is_scalar($replacement)) {
            return $replacement;
        }

        return (new ReturnedCliDumper())->dump($replacement);
    }
}
