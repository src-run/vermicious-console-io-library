<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Markup;

use SR\Console\Output\Exception\InvalidArgumentException;

class Markup implements MarkupColors, MarkupOptions
{
    /**
     * @var string[]
     */
    public const ACCEPTED_COLOURS = [
        self::C_DEFAULT,
        self::C_BLACK,
        self::C_RED,
        self::C_GREEN,
        self::C_YELLOW,
        self::C_BLUE,
        self::C_MAGENTA,
        self::C_CYAN,
        self::C_WHITE,
    ];

    /**
     * @var string[]
     */
    public const ACCEPTED_OPTIONS = [
        self::O_BOLD,
        self::O_UNDERSCORE,
        self::O_BLINK,
        self::O_REVERSE,
    ];

    /**
     * @var string|null
     */
    private $foregroundColour;

    /**
     * @var string|null
     */
    private $backgroundColour;

    /**
     * @var string[]
     */
    private $options;

    /**
     * @var bool
     */
    private $colourExplicit = false;

    /**
     * @var bool
     */
    private $emptyMarkupAllowed = false;

    /**
     * @param string|null $foregroundColour
     * @param string|null $backgroundColour
     * @param string[]    ...$options
     */
    public function __construct(string $foregroundColour = null, string $backgroundColour = null, string ...$options)
    {
        $this->setForeground($foregroundColour);
        $this->setBackground($backgroundColour);
        $this->setOptions(...$options);
    }

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function __invoke(string $value = null): string
    {
        return $this->markupValue($value);
    }

    /**
     * @param string|null $foregroundColour
     * @param string|null $backgroundColour
     * @param string[]    ...$options
     *
     * @return static
     */
    public static function create(string $foregroundColour = null, string $backgroundColour = null, string ...$options): self
    {
        return new static($foregroundColour, $backgroundColour, ...$options);
    }

    /**
     * @param string|null $foregroundColour
     * @param string|null $backgroundColour
     * @param string[]    ...$options
     *
     * @return static
     */
    public static function createExplicit(string $foregroundColour = null, string $backgroundColour = null, string ...$options): self
    {
        return self::create($foregroundColour, $backgroundColour, ...$options)
            ->setColourExplicit(true);
    }

    /**
     * @return string|null
     */
    public function foreground(): ?string
    {
        return $this->foregroundColour;
    }

    /**
     * @return bool
     */
    public function hasForeground(): bool
    {
        return null !== $this->foregroundColour;
    }

    /**
     * @param string|null $colour
     *
     * @return self
     */
    public function setForeground(string $colour = null): self
    {
        $this->foregroundColour = $this->sanitizeInputColour($colour);

        return $this;
    }

    /**
     * @return string|null
     */
    public function background(): ?string
    {
        return $this->backgroundColour;
    }

    /**
     * @return bool
     */
    public function hasBackground(): bool
    {
        return null !== $this->backgroundColour;
    }

    /**
     * @param string|null $colour
     *
     * @return self
     */
    public function setBackground(string $colour = null): self
    {
        $this->backgroundColour = $this->sanitizeInputColour($colour);

        return $this;
    }

    /**
     * @return string[]
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function hasOptions(): bool
    {
        return 0 !== count($this->options);
    }

    /**
     * @param string[] ...$options
     *
     * @return Markup
     */
    public function setOptions(string ...$options): self
    {
        $this->options = $this->sanitizeInputOptionList(...array_unique($options));
        sort($this->options);

        return $this;
    }

    /**
     * @param string[] ...$options
     *
     * @return self
     */
    public function addOptions(string ...$options): self
    {
        return $this->setOptions(...array_merge($this->options, $options));
    }

    /**
     * @return bool
     */
    public function isColourExplicit(): bool
    {
        return $this->colourExplicit;
    }

    /**
     * @param bool $colourExplicit
     *
     * @return self
     */
    public function setColourExplicit(bool $colourExplicit): self
    {
        $this->colourExplicit = $colourExplicit;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmptyMarkupAllowed(): bool
    {
        return $this->emptyMarkupAllowed;
    }

    /**
     * @param bool $allowEmptyMarkup
     *
     * @return self
     */
    public function setEmptyMarkupAllowed(bool $allowEmptyMarkup): self
    {
        $this->emptyMarkupAllowed = $allowEmptyMarkup;

        return $this;
    }

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function markupValue(string $value = null): string
    {
        if (!empty($markup = $this->attributesString()) || $this->isEmptyMarkupAllowed()) {
            return sprintf('<%s>%s</>', $markup, (string) $value);
        }

        return $value;
    }

    /**
     * @param string[] $lines
     *
     * @return string[]
     */
    public function markupLines(array $lines = []): array
    {
        return array_map(function (string $line): string {
            return $this->markupValue($line);
        }, $lines);
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        $attributes = [];

        if ($this->hasForeground() || $this->isColourExplicit()) {
            $attributes['fg'] = $this->foreground() ?? self::C_DEFAULT;
        }

        if ($this->hasBackground() || $this->isColourExplicit()) {
            $attributes['bg'] = $this->background() ?? self::C_DEFAULT;
        }

        if ($this->hasOptions()) {
            $attributes['options'] = implode(',', $this->options());
        }

        return $attributes;
    }

    /**
     * @return string
     */
    public function attributesString(): string
    {
        $attributes = $this->attributes();

        array_walk($attributes, function (string &$val, string $key) {
            $val = sprintf('%s=%s', $key, $val);
        });

        return implode(';', $attributes);
    }

    /**
     * @param string|null $colour
     *
     * @return string|null
     */
    private function sanitizeInputColour(string $colour = null): ?string
    {
        return $this->sanitizeInput('colour', $colour);
    }

    /**
     * @param string $option
     *
     * @return string
     */
    private function sanitizeInputOption(string $option): string
    {
        return $this->sanitizeInput('option', $option, false);
    }

    /**
     * @param string[]|mixed[] ...$options
     *
     * @return string[]
     */
    private function sanitizeInputOptionList(...$options): array
    {
        return array_map(function (string $o): string {
            return $this->sanitizeInputOption($o);
        }, $options);
    }

    /**
     * @param string      $context
     * @param string|null $input
     * @param bool        $nullable
     *
     * @return string|null
     */
    private function sanitizeInput(string $context, string $input = null, bool $nullable = true): ?string
    {
        $normalized = null === $input ? null : preg_replace('{[^a-z]}', '', mb_strtolower($input));
        $acceptable = 'colour' === $context ? self::ACCEPTED_COLOURS : self::ACCEPTED_OPTIONS;

        if (false === $nullable && true === empty($normalized)) {
            throw new InvalidArgumentException(...self::getExcArgsEmpty($context, $acceptable));
        }

        if (false === in_array($normalized, $acceptable, true) && null !== $normalized) {
            throw new InvalidArgumentException(...self::getExcArgsUnacceptable($context, $acceptable, $normalized));
        }

        return $normalized;
    }

    /**
     * @param string   $context
     * @param string[] $available
     *
     * @return mixed[]
     */
    private static function getExcArgsEmpty(string $context, array $available): array
    {
        return [
            'Invalid %s name provided: an empty value is not allowed (available %1$ss: %s).',
            $context,
            self::getAvailableValuesAsString($available),
        ];
    }

    /**
     * @param string   $context
     * @param string[] $available
     * @param string   $normalized
     *
     * @return mixed[]
     */
    private static function getExcArgsUnacceptable(string $context, array $available, string $normalized): array
    {
        return [
            'Invalid %s name provided: value "%s" is not allowed (available %1$ss: %s).',
            $context,
            $normalized,
            self::getAvailableValuesAsString($available),
        ];
    }

    /**
     * @param string[] $available
     *
     * @return string
     */
    private static function getAvailableValuesAsString(array $available): string
    {
        return implode(', ', array_map(function (string $i): string {
            return sprintf('"%s"', $i);
        }, $available));
    }
}
