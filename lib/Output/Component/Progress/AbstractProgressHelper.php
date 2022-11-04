<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Progress;

use SR\Console\Output\Component\Progress\Message\ProgressMessageHelper;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;
use SR\Exception\Runtime\RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class AbstractProgressHelper
{
    use StyleAwareInternalTrait;

    private int $newlinesAtCreate = 3;

    private int $newlinesAtFinish = 2;

    private ?string $barCharacter = '<fg=cyan>=</>';

    private ?string $emptyBarCharacter = '<fg=blue;options=bold>-</>';

    private ?string $progressCharacter = '<fg=cyan;options=bold>></>';

    /**
     * @var string[]
     */
    private array $formatLines = [
        ' [%bar%] (%current%/%max%)',
    ];

    private ?int $redrawsFreq;

    private ?float $redrawsMinSecsBetween;

    private ?float $redrawsMaxSecsBetween;

    private ?ProgressBar $progress = null;

    private ?ProgressMessageHelper $messages;

    public function __construct(StyleInterface $io, ?int $redrawsFreq = null, ?float $redrawsMinSecsBetween = 0.0000001, ?float $redrawsMaxSecsBetween = 1.0)
    {
        $this->setStyle($io);
        $this->setRedrawsFreq($redrawsFreq);
        $this->setRedrawsMinSecsBetween($redrawsMinSecsBetween);
        $this->setRedrawsMaxSecsBetween($redrawsMaxSecsBetween);
    }

    public function setRedrawsFreq(?int $redrawsFreq = null): self
    {
        $this->redrawsFreq = $redrawsFreq;

        return $this;
    }

    public function setRedrawsMinSecsBetween(?float $redrawsMinSecsBetween = 0.0000001): self
    {
        $this->redrawsMinSecsBetween = $redrawsMinSecsBetween;

        return $this;
    }

    public function setRedrawsMaxSecsBetween(?float $redrawsMaxSecsBetween = 0.0000001): self
    {
        $this->redrawsMaxSecsBetween = $redrawsMaxSecsBetween;

        return $this;
    }

    public function setNewlinesAtCreate(int $newlines): self
    {
        $this->newlinesAtCreate = $newlines;

        return $this;
    }

    public function setNewlinesAtFinish(int $newlines): self
    {
        $this->newlinesAtFinish = $newlines;

        return $this;
    }

    public function setBarCharacter(string $character = null): self
    {
        $this->barCharacter = $character;

        return $this;
    }

    public function setEmptyBarCharacter(string $character = null): self
    {
        $this->emptyBarCharacter = $character;

        return $this;
    }

    public function setProgressCharacter(string $character = null): self
    {
        $this->progressCharacter = $character;

        return $this;
    }

    /**
     * @param string[] $formatLines
     */
    public function setFormatLines(array $formatLines): self
    {
        $this->formatLines = $formatLines;

        return $this;
    }

    public function create(int $steps = null, string $context = null): self
    {
        $this->ensureProgressStopped();
        $this->initProgress($this->style()->newline($this->newlinesAtCreate)->progress($steps), $context);

        return $this;
    }

    public function step(int $count = 1): self
    {
        $this->ensureProgressStarted();
        $this->progressBar()->advance($count);

        return $this;
    }

    public function finish(): self
    {
        $this->ensureProgressStarted();
        $this->progressBar()->finish();
        $this->style()->newline($this->newlinesAtFinish);
        $this->nullProgress();

        return $this;
    }

    public function display(): self
    {
        $this->progressBar()->display();

        return $this;
    }

    public function messages(): ProgressMessageHelper
    {
        $this->ensureProgressStarted();

        return $this->messages;
    }

    public function progressBar(): ProgressBar
    {
        $this->ensureProgressStarted();

        return $this->progress;
    }

    protected function ensureProgressStarted(): void
    {
        if (null === $this->progress || null === $this->messages) {
            throw new RuntimeException('You must start an active progress bar before acting on it!');
        }
    }

    protected function ensureProgressStopped(): void
    {
        try {
            $this->ensureProgressStarted();
        } catch (RuntimeException $e) {
            return;
        }

        throw new RuntimeException('You must stop an active progress bar before starting a new one!');
    }

    private function initProgress(ProgressBar $progress, string $context = null): void
    {
        if (null !== $this->barCharacter) {
            $progress->setBarCharacter($this->barCharacter);
        }

        if (null !== $this->emptyBarCharacter) {
            $progress->setEmptyBarCharacter($this->emptyBarCharacter);
        }

        if (null !== $this->progressCharacter) {
            $progress->setProgressCharacter($this->progressCharacter);
        }

        if (0 !== count($this->formatLines)) {
            $progress->setFormat(implode(PHP_EOL, $this->formatLines) . PHP_EOL);
        }

        $progress->setRedrawFrequency($this->redrawsFreq);

        if (null !== $this->redrawsMinSecsBetween) {
            $progress->minSecondsBetweenRedraws($this->redrawsMinSecsBetween);
        }

        if (null !== $this->redrawsMaxSecsBetween) {
            $progress->maxSecondsBetweenRedraws($this->redrawsMaxSecsBetween);
        }

        $progress->start();

        $this->progress = $progress;
        $this->messages = new ProgressMessageHelper($this);

        if (null !== $context) {
            $this->messages()->context($context);
            $this->display();
        }
    }

    private function nullProgress(): void
    {
        $this->progress = $this->messages = null;
    }
}
