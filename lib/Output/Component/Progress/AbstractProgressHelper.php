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

    /**
     * @var int
     */
    private $newlinesAtCreate = 3;

    /**
     * @var int
     */
    private $newlinesAtFinish = 2;

    /**
     * @var string|null
     */
    private $barCharacter = '<fg=cyan>=</>';

    /**
     * @var string|null
     */
    private $emptyBarCharacter = '<fg=blue;options=bold>-</>';

    /**
     * @var string|null
     */
    private $progressCharacter = '<fg=cyan;options=bold>></>';

    /**
     * @var string[]
     */
    private $formatLines = [
        ' [%bar%] (%current%/%max%)',
    ];

    /**
     * @var ProgressBar|null
     */
    private $progress;

    /**
     * @var ProgressMessageHelper|null
     */
    private $messages;

    /**
     * @param StyleInterface $io
     */
    public function __construct(StyleInterface $io)
    {
        $this->setStyle($io);
    }

    /**
     * @param int $newlines
     *
     * @return self
     */
    public function setNewlinesAtCreate(int $newlines): self
    {
        $this->newlinesAtCreate = $newlines;

        return $this;
    }

    /**
     * @param int $newlines
     *
     * @return self
     */
    public function setNewlinesAtFinish(int $newlines): self
    {
        $this->newlinesAtFinish = $newlines;

        return $this;
    }

    /**
     * @param string $character|null
     *
     * @return self
     */
    public function setBarCharacter(string $character = null): self
    {
        $this->barCharacter = $character;

        return $this;
    }

    /**
     * @param string $character|null
     *
     * @return self
     */
    public function setEmptyBarCharacter(string $character = null): self
    {
        $this->emptyBarCharacter = $character;

        return $this;
    }

    /**
     * @param string $character|null
     *
     * @return self
     */
    public function setProgressCharacter(string $character = null): self
    {
        $this->progressCharacter = $character;

        return $this;
    }

    /**
     * @param string[] $formatLines
     *
     * @return self
     */
    public function setFormatLines(array $formatLines): self
    {
        $this->formatLines = $formatLines;

        return $this;
    }

    /**
     * @param int|null    $steps
     * @param string|null $context
     *
     * @return self
     */
    public function create(int $steps = null, string $context = null): self
    {
        $this->ensureProgressStopped();
        $this->initProgress($this->style()->newline($this->newlinesAtCreate)->progress($steps), $context);

        return $this;
    }

    /**
     * @param int $count
     *
     * @return self
     */
    public function step(int $count = 1): self
    {
        $this->ensureProgressStarted();
        $this->progressBar()->advance($count);

        return $this;
    }

    /**
     * @return self
     */
    public function finish(): self
    {
        $this->ensureProgressStarted();
        $this->progressBar()->finish();
        $this->style()->newline($this->newlinesAtFinish);
        $this->nullProgress();

        return $this;
    }

    /**
     * @return self
     */
    public function display(): self
    {
        $this->progressBar()->display();

        return $this;
    }

    /**
     * @return ProgressMessageHelper
     */
    public function messages(): ProgressMessageHelper
    {
        $this->ensureProgressStarted();

        return $this->messages;
    }

    /**
     * @return ProgressBar
     */
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

    /**
     * @param ProgressBar $progress
     * @param string|null $context
     */
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
            $progress->setFormat(implode(PHP_EOL, $this->formatLines).PHP_EOL);
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
