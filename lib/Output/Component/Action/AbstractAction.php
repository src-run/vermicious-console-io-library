<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Action;

use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Console\Output\Exception\RuntimeException;
use SR\Console\Output\Markup\Markup;
use SR\Console\Output\Style\StyleAwareExternalTrait;
use SR\Console\Output\Style\StyleInterface;

abstract class AbstractAction
{
    use StyleAwareExternalTrait;

    /**
     * @var string
     */
    private const STATE_INACTIVE = 'inactive';

    /**
     * @var string
     */
    private const STATE_ACTION = 'action';

    /**
     * @var string
     */
    private const STATE_PREFIX = 'prefix';

    /**
     * @var string
     */
    private const STATE_RESULT = 'result';

    /**
     * @var string
     */
    private const STATE_EXTRAS = 'extras';

    /**
     * @var string
     */
    private $state = self::STATE_INACTIVE;

    /**
     * @var \Closure|null
     */
    private $actionFormatter;

    /**
     * @var Markup
     */
    private $actionDefMarkup;

    /**
     * @var \Closure|null
     */
    private $prefixFormatter;

    /**
     * @var Markup
     */
    private $prefixDefMarkup;

    /**
     * @var \Closure|null
     */
    private $resultFormatter;

    /**
     * @var Markup
     */
    private $resultDefMarkup;

    /**
     * @var \Closure|null
     */
    private $extrasFormatter;

    /**
     * @var Markup
     */
    private $extrasDefMarkup;

    /**
     * @var bool
     */
    private $supportExtras;

    /**
     * @var int
     */
    private $finalNewlines;

    /**
     * @param StyleInterface|null $style
     * @param Markup|null         $actionDefMarkup
     * @param \Closure|null       $actionFormatter
     * @param Markup|null         $prefixDefMarkup
     * @param \Closure|null       $prefixFormatter
     * @param Markup|null         $resultDefMarkup
     * @param \Closure|null       $resultFormatter
     * @param Markup|null         $extrasDefMarkup
     * @param \Closure|null       $extrasFormatter
     */
    public function __construct(
        StyleInterface $style = null,
        Markup         $actionDefMarkup = null,
        ?\Closure      $actionFormatter = null,
        Markup         $prefixDefMarkup = null,
        ?\Closure      $prefixFormatter = null,
        Markup         $resultDefMarkup = null,
        ?\Closure      $resultFormatter = null,
        Markup         $extrasDefMarkup = null,
        ?\Closure      $extrasFormatter = null
    ) {
        $this->setStyle($style);
        $this->actionDefMarkup = $actionDefMarkup ?? Markup::createExplicit();
        $this->actionFormatter = $actionFormatter;
        $this->prefixDefMarkup = $prefixDefMarkup ?? Markup::createExplicit();
        $this->prefixFormatter = $prefixFormatter;
        $this->resultDefMarkup = $resultDefMarkup ?? Markup::createExplicit();
        $this->resultFormatter = $resultFormatter;
        $this->extrasDefMarkup = $extrasDefMarkup ?? Markup::createExplicit();
        $this->extrasFormatter = $extrasFormatter;

        $this->setNewlinesCount();
        $this->setSupportExtras();
    }

    /**
     * @param string|null $type
     * @param array       ...$constructorArguments
     *
     * @return AbstractAction
     */
    public static function createActionType(string $type = null, ...$constructorArguments): self
    {
        if (class_exists($class = sprintf('%s\%sActionHelper', __NAMESPACE__, ucfirst($type ?? '')))) {
            return new $class(...$constructorArguments);
        }

        throw new InvalidArgumentException('Unable to find action helper of type "%s".', $type ?? 'default');
    }

    /**
     * @param int|null $newlinesCount
     *
     * @return self
     */
    public function setNewlinesCount(int $newlinesCount = null): self
    {
        $this->requireState(__METHOD__, self::STATE_INACTIVE, self::STATE_ACTION);
        $this->finalNewlines = $newlinesCount ?? 2;

        return $this;
    }

    /**
     * @param bool|null $supportExtras
     *
     * @return self
     */
    public function setSupportExtras(bool $supportExtras = null): self
    {
        $this->requireState(__METHOD__, self::STATE_INACTIVE, self::STATE_ACTION);
        $this->supportExtras = $supportExtras ?? false;

        return $this;
    }

    /**
     * @param string $action
     * @param Markup $markup
     *
     * @return self
     */
    public function action(string $action, Markup $markup = null): self
    {
        return $this->invokeStateful(__METHOD__, function () use ($action, $markup) {
            $this->style->prependText();
            $this->style->write(($this->actionFormatter)($markup ?? $this->actionDefMarkup, $action));
        }, self::STATE_ACTION, self::STATE_INACTIVE);
    }

    /**
     * @param string      $prefix
     * @param Markup|null $markup
     *
     * @return self
     */
    public function resultPrefix(string $prefix, Markup $markup = null): self
    {
        return $this->invokeStateful(__METHOD__, function () use ($prefix, $markup) {
            $this->style->write(($this->prefixFormatter)($markup ?? $this->prefixDefMarkup, $prefix));
        }, self::STATE_PREFIX, [self::STATE_ACTION]);
    }

    /**
     * @param string $result
     * @param Markup $markup
     *
     * @return self
     */
    public function result(string $result, Markup $markup = null): self
    {
        $this->invokeStateful(__METHOD__, function () use ($markup, $result) {
            $this->style->write(($this->resultFormatter)($markup ?? $this->resultDefMarkup, $result));
        }, self::STATE_RESULT, [self::STATE_ACTION, self::STATE_PREFIX]);

        if (false === $this->supportExtras) {
            $this->complete();
        }

        return $this;
    }

    /**
     * @param string $result
     *
     * @return self
     */
    abstract public function resultDone(string $result = 'done'): self;

    /**
     * @param string $result
     *
     * @return self
     */
    abstract public function resultOkay(string $result = 'okay'): self;

    /**
     * @param string $result
     *
     * @return self
     */
    abstract public function resultWarn(string $result = 'warn'): self;

    /**
     * @param string $result
     *
     * @return self
     */
    abstract public function resultStop(string $result = 'stop'): self;

    /**
     * @param string $result
     *
     * @return self
     */
    abstract public function resultFail(string $result = 'fail'): self;

    /**
     * @param string[] ...$extras
     *
     * @return self
     */
    public function extras(string ...$extras): self
    {
        if (false === $this->supportExtras) {
            throw new RuntimeException('Action extras disabled: enable by passing "true" to "extrasEnabled()"');
        }

        if (self::STATE_RESULT === $this->state) {
            $this->style->write(' ');
        }

        return $this
            ->invokeStateful(__METHOD__, function () use ($extras) {
                $this->style->write(($this->extrasFormatter)($this->extrasDefMarkup, ...$extras));
            }, self::STATE_EXTRAS, [self::STATE_ACTION, self::STATE_PREFIX, self::STATE_RESULT])
            ->complete();
    }

    /**
     * @return self
     */
    public function complete(): self
    {
        return $this->invokeStateful(__METHOD__, function () {
            $this->style->newline($this->finalNewlines);
        }, self::STATE_INACTIVE, [self::STATE_ACTION, self::STATE_PREFIX, self::STATE_RESULT, self::STATE_EXTRAS]);
    }

    /**
     * @param string          $context
     * @param \Closure        $closure
     * @param string          $afterAssignment
     * @param string|string[] $beforeRequire
     *
     * @return self
     */
    private function invokeStateful(string $context, \Closure $closure, string $afterAssignment, $beforeRequire): self
    {
        $this->requireState($context, ...(array) $beforeRequire);
        $closure();

        if (null !== $afterAssignment) {
            $this->state = $afterAssignment;
        }

        return $this;
    }

    /**
     * @param string   $context
     * @param string[] ...$supportedStates
     *
     * @return self
     */
    private function requireState(string $context, string ...$supportedStates): self
    {
        $matched = array_filter($supportedStates, function (string $state): bool {
            return $this->state === $state;
        });

        if (0 === count($matched)) {
            throw new RuntimeException('Cannot call %s method in state "%s" (acceptable states: %s).', $context,
                $this->state, implode(', ', $supportedStates));
        }

        return $this;
    }
}
