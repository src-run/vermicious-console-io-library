<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Utility\State;

use SR\Console\Output\Exception\StateException;

final class State
{
    /**
     * @var string|null
     */
    private $state;

    public function __construct(string $state = null)
    {
        $this->state = $state;
    }

    public function isState(string $state): bool
    {
        return $this->getState() === $state;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function stateRequirements(string $context, string ...$stateConstraints): self
    {
        $matched = array_filter($stateConstraints, function (string $state): bool {
            return $this->isState($state);
        });

        if (0 === count($matched)) {
            throw new StateException('Cannot call %s() method in state "%s" (acceptable states: %s).', $context, $this->getState(), implode(', ', $stateConstraints));
        }

        return $this;
    }

    /**
     * @param string|string[] $stateConstraints
     */
    public function stateRequireRunAndSetAction(string $context, \Closure $closure, $stateConstraints, string $stateAssignment = null): self
    {
        $this->stateRequirements($context, ...(array) $stateConstraints);

        $closure();

        if (null !== $stateAssignment) {
            $this->setState($stateAssignment);
        }

        return $this;
    }

    /**
     * @param string|string[] $stateConstraints
     */
    public function stateInverseRequireRunAndSetAction(string $context, \Closure $closure, $stateConstraints): self
    {
        try {
            $this->stateRequirements($context, ...(array) $stateConstraints);
        } catch (StateException $exception) {
            $closure();
        }

        return $this;
    }

    /**
     * @param string|string[] $stateConstraints
     */
    public function stateConditionalSetRunAction(string $context, \Closure $closure, $stateConstraints, string $stateAssignment = null): self
    {
        try {
            $this->stateRequirements($context, ...(array) $stateConstraints);
        } catch (StateException $exception) {
            return $this;
        }

        return $this->stateRequireRunAndSetAction($context, $closure, $stateConstraints, $stateAssignment);
    }
}
