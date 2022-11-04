<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Input\Component\Question;

use SR\Console\Input\Component\Question\Answer\AnswerInterface;
use SR\Console\Input\Component\Question\Answer\BooleanAnswer;
use SR\Console\Input\Component\Question\Answer\ChoiceAnswer;
use SR\Console\Input\Component\Question\Answer\MultipleChoiceAnswer;
use SR\Console\Input\Component\Question\Answer\StringAnswer;
use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Console\Output\Exception\RuntimeException;
use SR\Console\Output\Style\Style;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;
use SR\Console\Output\Utility\Terminal\Terminal;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class QuestionHelper
{
    use StyleAwareInternalTrait;

    /**
     * @var resource|null
     */
    private $inputStream;

    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
    }

    /**
     * @return AnswerInterface|StringAnswer
     */
    public function question(string $question, string $default = null, \Closure $validator = null, \Closure $normalizer = null): StringAnswer
    {
        return $this->handleQuestion(new Question($question, $default), $validator, $normalizer);
    }

    /**
     * @return AnswerInterface|StringAnswer
     */
    public function hiddenQuestion(string $question, string $default = null, \Closure $validator = null, \Closure $normalizer = null): StringAnswer
    {
        return $this->handleQuestion(new Question($question, $default), $validator, $normalizer, function (Question $question) {
            $question->setHidden(true);
        });
    }

    /**
     * @return AnswerInterface|BooleanAnswer
     */
    public function confirm(string $question, bool $default = true, \Closure $validator = null, \Closure $normalizer = null): BooleanAnswer
    {
        return $this->handleQuestion(new ConfirmationQuestion($question, $default), $validator, $normalizer);
    }

    /**
     * @return AnswerInterface|ChoiceAnswer|MultipleChoiceAnswer
     */
    public function choice(string $question, array $choices, string $default = null, bool $multipleChoice = false, \Closure $validator = null, \Closure $normalizer = null, iterable $completionValues = null): AnswerInterface
    {
        return $this->handleQuestion(
            new ChoiceQuestion($question, $choices, self::resolveDefault($choices, $default, $multipleChoice)),
            $validator,
            $normalizer,
            function (ChoiceQuestion $question) use ($completionValues): void {
                if (null !== $completionValues) {
                    $question->setAutocompleterValues($completionValues);
                }
            },
            $multipleChoice
        );
    }

    /**
     * @return AnswerInterface|ChoiceAnswer|MultipleChoiceAnswer
     */
    public function hiddenChoice(string $question, array $choices, string $default = null, bool $multipleChoice = false, \Closure $validator = null, \Closure $normalizer = null): AnswerInterface
    {
        return $this->handleQuestion(
            new ChoiceQuestion($question, $choices, self::resolveDefault($choices, $default, $multipleChoice)),
            $validator,
            $normalizer,
            function (ChoiceQuestion $question): void {
                $question->setAutocompleterValues(null);
                $question->setHidden(true);
            },
            $multipleChoice
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function writeError(\Exception $exception = null, OutputInterface $output = null)
    {
        $s = $this->style();

        if (null !== $output) {
            $s = new Style($s->getInput(), $output);
        }

        $s->newline();
        $s->error(sprintf('[%s] %s', get_class($exception), $exception->getMessage()));
    }

    /**
     * {@inheritdoc}
     */
    protected function writePrompt(OutputInterface $o, Question $question)
    {
        $this->writeQuestionText($question, $o);

        if ($question instanceof ChoiceQuestion) {
            $this->writeQuestionChoices($question, $o);
        }

        $o->write(' > ');
    }

    /**
     * @return AnswerInterface|ChoiceAnswer|MultipleChoiceAnswer|StringAnswer|BooleanAnswer
     */
    private function handleQuestion(Question $question, \Closure $validator = null, \Closure $normalizer = null, \Closure $configurator = null, bool $multipleChoice = false): AnswerInterface
    {
        $this->configureQuestion($question, $validator, $normalizer, $configurator, $multipleChoice);

        if ($this->style()->getInput()->isInteractive()) {
            $this->style()->prependBlock();
        }

        try {
            $answer = $this->interviewQuestion($question);
        } catch (RuntimeException $exception) {
            throw $exception;
        }

        if ($this->style()->getInput()->isInteractive()) {
            $this->style()->newLine();
        }

        return $answer ?? self::createAnswer($question, null, false, true);
    }

    private function interviewQuestion(Question $question): AnswerInterface
    {
        return $this->style()->getInput()->isInteractive()
            ? $this->interviewQuestionAsInteractiveValidated($question)
            : $this->interviewQuestionNoInteractive($question);
    }

    private function interviewQuestionAsInteractiveValidated(Question $question): AnswerInterface
    {
        $i = $this->style()->getInput();
        $o = $this->style()->getOutput();
        $o = $o instanceof ConsoleOutputInterface ? $o->getErrorOutput() : $o;

        if ($i instanceof StreamableInputInterface && is_resource($stream = $i->getStream())) {
            $this->inputStream = $stream;
        }

        $priorError = null;
        $iterations = $question->getMaxAttempts() ?? 100;

        while ($iterations-- > 0) {
            if (null !== $priorError) {
                $this->writeError($priorError, $o);
            }

            try {
                return (self::getQuestionValidationClosure($question))(
                    $this->interviewQuestionAsInteractive($question, $o)
                );
            } catch (RuntimeException $exception) {
                throw $exception;
            } catch (\Exception $exception) {
                $priorError = $exception;
            }
        }

        throw new RuntimeException('Failed to interview question "%s": %s', $question->getQuestion(), $priorError->getMessage(), $priorError);
    }

    private function interviewQuestionNoInteractive(Question $question): AnswerInterface
    {
        if ($question instanceof ChoiceQuestion) {
            $choices = $question->getChoices();

            if (isset($choices[$question->getDefault()])) {
                return (self::getQuestionValidationClosure($question))(
                    self::createAnswer($question, $choices[$question->getDefault()], true, false)
                );
            }

            if (count(self::parseAnswerChoices($answer = self::createAnswer($question, $question->getDefault(), true, false), $question->isMultiselect())) > 1) {
                return (self::getQuestionValidationClosure($question))(
                    $answer
                );
            }

            throw new RuntimeException('Configured default "%s" is not an available choice.', $question->getDefault());
        }

        return (self::getQuestionValidationClosure($question))(
            self::createAnswer($question, $question->getDefault(), true, false)
        );
    }

    /**
     * @return StringAnswer
     */
    private function interviewQuestionAsInteractive(Question $question, OutputInterface $o): AnswerInterface
    {
        $this->writePrompt($o, $question);

        $isDefault = false;
        $userInput = null === $question->getAutocompleterValues() || false === Terminal::stty()
            ? trim($this->readResponseNonAutoComplete($question, $o))
            : trim($this->readResponseUseAutoComplete($question, $o));

        if (0 === mb_strlen($userInput)) {
            $userInput = $question->getDefault();
            $isDefault = true;
        }

        if (null !== $normalizer = $question->getNormalizer()) {
            $userInput = $normalizer($userInput);
        }

        return $this->createAnswer($question, $userInput, $isDefault, true);
    }

    /**
     * @return mixed
     */
    private function readResponseUseAutoComplete(Question $question, OutputInterface $o)
    {
        $values = $question->getAutocompleterValues();

        return $this->performInputAutoCompletion(
            $o,
            $question,
            $this->inputStream ?? STDIN,
            $values instanceof \Traversable ? iterator_to_array($values) : $values
        );
    }

    /**
     * @return mixed
     */
    private function readResponseNonAutoComplete(Question $question, OutputInterface $o)
    {
        if (false === $response = ($question->isHidden() ? $this->readResponseHidden($question, $o) : false)) {
            $response = fgets($this->inputStream ?? STDIN, 4096);
        }

        return $response;
    }

    private function readResponseHidden(Question $question, OutputInterface $o): string
    {
        try {
            $response = $this->getHiddenResponse($o);
        } catch (RuntimeException $e) {
            if (!$question->isHiddenFallback()) {
                throw $e;
            }
        }

        return $response ?? '';
    }

    private function performInputAutoCompletion(OutputInterface $output, Question $question, $inputStream, array $autoCompleteValues): string
    {
        $ret = '';
        $all = '';

        $i = 0;
        $ofs = -1;
        $matchesSet = $autoCompleteValues;
        $matchesLen = count($matchesSet);

        $sttyMode = shell_exec('stty -g');

        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');

        // Add highlighted text style
        $output->getFormatter()->setStyle('hl', new OutputFormatterStyle('black', 'white'));

        // Read a keypress
        while (!feof($inputStream)) {
            $c = fread($inputStream, 1);

            // Backspace Character
            if ("\177" === $c || "\010" === $c) {
                // Pop the last character off the end of our string
                if (0 !== $i) {
                    $ret = mb_substr($ret, 0, mb_strlen($ret) - 1);
                    $all = mb_substr($all, 0, mb_strlen($all) - 1);
                }
                if (0 === $matchesLen && 0 !== $i) {
                    --$i;

                    // Move cursor backwards
                    $output->write("\033[1D");
                }

                if (0 === $i) {
                    $ofs = -1;
                    $matchesSet = $autoCompleteValues;
                    $matchesLen = count($matchesSet);
                } else {
                    $matchesLen = 0;
                }
            } elseif ("\033" === $c) {
                // Did we read an escape sequence?
                $c .= fread($inputStream, 2);

                // A = Up Arrow. B = Down Arrow
                if (isset($c[2]) && ('A' === $c[2] || 'B' === $c[2])) {
                    if ('A' === $c[2] && -1 === $ofs) {
                        $ofs = 0;
                    }

                    if (0 === $matchesLen) {
                        continue;
                    }

                    $ofs += ('A' === $c[2]) ? -1 : 1;
                    $ofs = ($matchesLen + $ofs) % $matchesLen;
                }
            } elseif (ord($c) < 32) {
                if ("\t" === $c || "\n" === $c) {
                    if ($matchesLen > 0 && -1 !== $ofs) {
                        $last = self::extractLastAutoCompleteMultiChoiceInput($question, $all) ?? $ret;
                        $ret = $matchesSet[$ofs];
                        $all .= mb_substr($ret, mb_strlen($last));
                        // Echo out remaining chars for current match
                        $output->write(mb_substr($ret, mb_strlen($last)));
                        $i = mb_strlen($ret);
                    }

                    if ("\n" === $c) {
                        $output->write($c);
                        break;
                    }

                    $matchesLen = 0;
                }

                continue;
            } else {
                $output->write($c);
                $ret .= $c;
                $all .= $c;
                ++$i;

                $matchesLen = 0;
                $matchesSet = array_slice($matchesSet, 0, 20);
                $ofs = 0;
                $last = self::extractLastAutoCompleteMultiChoiceInput($question, $all);

                foreach ($autoCompleteValues as $value) {
                    if (null !== $match = $this->resolveAutoCompleteMatch($value, $all, $last)) {
                        $matchesSet[$matchesLen++] = $match;
                    }
                }
            }

            // Erase characters from cursor to end of line
            $output->write("\033[K");

            if ($matchesLen > 0 && -1 !== $ofs) {
                $last = self::extractLastAutoCompleteMultiChoiceInput($question, $all) ?? $ret;
                // Save cursor position
                $output->write("\0337");
                // Write highlighted text
                $output->write('<hl>' . OutputFormatter::escapeTrailingBackslash(mb_substr($matchesSet[$ofs], mb_strlen($last))) . '</hl>');
                // Restore cursor position
                $output->write("\0338");
            }
        }

        // Reset stty so it behaves normally again
        shell_exec(sprintf('stty %s', $sttyMode));

        return $all;
    }

    private function resolveAutoCompleteMatch(string $completionValue, string $input, ?string $prior): ?string
    {
        if ($input === mb_substr($completionValue, 0, mb_strlen($input))) {
            return $completionValue;
        }

        if (null !== $prior && $prior === mb_substr($completionValue, 0, mb_strlen($prior))) {
            return $completionValue;
        }

        return null;
    }

    /**
     * @param OutputInterface $output An Output instance
     *
     * @throws RuntimeException In case the fallback is deactivated and the response cannot be hidden
     */
    private function getHiddenResponse(OutputInterface $output): string
    {
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            if (Terminal::stty()) {
                $sttyMode = shell_exec('stty -g');

                shell_exec('stty -echo');
                $value = fgets($this->inputStream ?? STDIN, 4096);
                shell_exec(sprintf('stty %s', $sttyMode));

                if (false === $value) {
                    throw new RuntimeException('Aborted');
                }

                $value = trim($value);
                $output->writeln('');

                return $value;
            }

            if (null !== $shell = Terminal::shell()) {
                $value = rtrim(shell_exec(
                    vsprintf("/usr/bin/env %s -c 'stty -echo; %s; stty echo; echo \$HIDDEN_INPUT'", [
                        $shell,
                        Terminal::isShell('csh') ? 'set HIDDEN_INPUT = $<' : 'read -r HIDDEN_INPUT',
                    ])
                ));
                $output->writeln('');

                return $value;
            }
        }

        throw new RuntimeException('Unable to hide the response.');
    }

    private function configureQuestion(Question $question, ?\Closure $validate, ?\Closure $normalizer, ?\Closure $configurator, bool $multipleChoice): void
    {
        if ($question instanceof ChoiceQuestion) {
            $question->setMultiselect($multipleChoice);
            $validate = function (AnswerInterface $answer) use ($question, $validate, $multipleChoice): AnswerInterface {
                return ($validate ?? function (AnswerInterface $answer): AnswerInterface { return $answer; })(
                    $this->validateChoiceResult($question, $answer, $multipleChoice)
                );
            };
        }

        if (null !== $validate) {
            $question->setValidator($validate);
        }

        if (null !== $normalizer) {
            $question->setNormalizer($normalizer);
        }

        if (null !== $configurator) {
            $configurator($question);
        }
    }

    /**
     * @param string|int|null $default
     *
     * @return string|int|null
     */
    private static function resolveDefault(array $choices, $default = null, bool $multiChoice = false)
    {
        if (!$multiChoice) {
            return self::resolveDefaultIndex($choices, $default);
        }

        return implode(',', array_filter(array_map(function ($d) use ($choices) {
            return self::resolveDefaultIndex($choices, trim($d));
        }, explode(',', $default ?? '')), function ($d): bool {
            return null !== $d;
        })) ?: null;
    }

    /**
     * @param string|int|null $default
     *
     * @return string|int|null
     */
    private static function resolveDefaultIndex(array $choices, $default = null)
    {
        if (null !== $default) {
            if (isset(array_flip($choices)[$default])) {
                return array_flip($choices)[$default];
            }

            if (isset($choices[$default]) && false !== $v = array_search($choices[$default], $choices, true)) {
                return $v;
            }
        }

        return null;
    }

    private static function parseAnswerChoices(AnswerInterface $answer, bool $multipleChoice): array
    {
        if ($multipleChoice && 1 === preg_match('/^[^,]+(?:,[^,]+)*$/', $answer->stringifyAnswer(), $found)) {
            return explode(',', preg_replace('{\s*,\s*}', ',', $answer->stringifyAnswer()));
        }

        return [$answer->stringifyAnswer()];
    }

    /**
     * @return ChoiceAnswer|MultipleChoiceAnswer
     */
    private function validateChoiceResult(ChoiceQuestion $question, AnswerInterface $answer, bool $multipleChoice)
    {
        $availableChoices = $question->getChoices();
        $validatedChoices = [];

        foreach (self::parseAnswerChoices($answer, $multipleChoice) as $choice) {
            $found = [];

            foreach ($availableChoices as $index => $value) {
                if ($choice === $index || $choice === $value) {
                    $found[$index] = $value;
                }
            }

            if (count($found) > 1) {
                throw new InvalidArgumentException('The provided answer is ambiguous. Value should be one of %s.', self::stringifyQuestionChoices($found));
            }

            if (!self::isAssociativeArray($availableChoices) && (int) $choice === $choice && isset($availableChoices[(int) $choice])) {
                $found[(int) $choice] = $availableChoices[(int) $choice];
            } elseif (isset($availableChoices[$choice])) {
                $found[$choice] = $availableChoices[$choice];
            }

            if (0 === count($found) && empty($choice)) {
                throw new InvalidArgumentException('Invalid empty choice answer provided. Available choices: %s.', self::stringifyQuestionChoices($question));
            }

            if (0 === count($found)) {
                throw new InvalidArgumentException('Invalid choice answer "%s" provided. Available choices: %s.', $choice, self::stringifyQuestionChoices($question));
            }

            foreach ($found as $index => $value) {
                $validatedChoices[$index] = $value;
            }
        }

        return $multipleChoice
            ? self::createMultipleChoiceAnswer($question, $answer, $validatedChoices)
            : self::createSingularChoiceAnswer($question, $answer, $validatedChoices);
    }

    private static function createMultipleChoiceAnswer(Question $question, AnswerInterface $answer, array $choices): MultipleChoiceAnswer
    {
        return new MultipleChoiceAnswer(
            $question, $choices, $answer->isDefault(), $answer->isInteractive()
        );
    }

    private static function createSingularChoiceAnswer(Question $question, AnswerInterface $answer, array $choices): ChoiceAnswer
    {
        return new ChoiceAnswer(
            $question, current($choices), key($choices), $answer->isDefault(), $answer->isInteractive()
        );
    }

    private static function getQuestionValidationClosure(Question $question): \Closure
    {
        return $question->getValidator() ?? function ($answer) {
            return $answer;
        };
    }

    private static function extractLastAutoCompleteMultiChoiceInput(Question $question, string $choices): ?string
    {
        if (!$question instanceof ChoiceQuestion || !$question->isMultiselect()) {
            return null;
        }

        $multiChoices = array_filter(array_map(function (string $c) {
            return trim($c);
        }, explode(',', $choices)), function (string $c) {
            return !empty($c);
        });

        return array_pop($multiChoices);
    }

    /**
     * @param ChoiceQuestion|array $choices
     */
    private static function stringifyQuestionChoices($choices): string
    {
        $choices = $choices instanceof ChoiceQuestion ? $choices->getChoices() : (array) $choices;
        $strings = array_map(function ($value) use ($choices): string {
            return sprintf('"%s" or "%s"', $value, $choices[$value]);
        }, array_keys($choices));

        return count($strings) > 2
            ? preg_replace('{(.+".+?"), (.+?)$}', '\1, and \2', implode(', ', $strings))
            : preg_replace('{(.+".+?"), (.+?)$}', '\1 and \2', implode(', ', $strings));
    }

    private function writeQuestionText(Question $question, OutputInterface $o): void
    {
        $o->writeln(self::formatQuestionPromptText($question, function (string $text, ...$replacements): string {
            return vsprintf($text, array_map(function ($question): string {
                return $question instanceof Question
                    ? OutputFormatter::escapeTrailingBackslash($question->getQuestion())
                    : OutputFormatter::escape($question);
            }, $replacements));
        }));
    }

    private static function formatQuestionPromptText(Question $question, \Closure $formatter): string
    {
        if (null === $question->getDefault()) {
            return $formatter(' <info>%s</info>:', $question);
        }

        if ($question instanceof ConfirmationQuestion) {
            return $formatter(
                ' <info>%s (yes/no)</info> [<comment>%s</comment>]:',
                $question,
                $question->getDefault() ? 'yes' : 'no'
            );
        }

        if ($question instanceof ChoiceQuestion && $question->isMultiselect()) {
            return $formatter(
                ' <info>%s</info> [%s]:',
                $question,
                implode(', ', array_map(function (string $choice) use ($question) {
                    return sprintf('<comment>%s</comment>', $question->getChoices()[trim($choice)] ?? $choice);
                }, explode(',', $question->getDefault() ?? '')))
            );
        }

        if ($question instanceof ChoiceQuestion) {
            return $formatter(
                ' <info>%s</info> [<comment>%s</comment>]:',
                $question,
                $question->getChoices()[$question->getDefault()] ?? $question->getDefault()
            );
        }

        return $formatter(
            ' <info>%s</info> [<comment>%s</comment>]:',
            $question,
            $question->getDefault()
        );
    }

    private function writeQuestionChoices(ChoiceQuestion $question, OutputInterface $o): void
    {
        foreach ($question->getChoices() as $key => $value) {
            $o->writeln(sprintf(sprintf(
                '  [<comment>%%-%ds</comment>] %%s', max(array_map('strlen', array_keys($question->getChoices())))
            ), $key, $value));
        }
    }

    /**
     * @param mixed|null $answer
     */
    private static function createAnswer(Question $question, $answer = null, bool $default = false, bool $interactive = true): AnswerInterface
    {
        return $question instanceof ConfirmationQuestion
            ? new BooleanAnswer($question, $answer, $default, $interactive)
            : new StringAnswer($question, $answer, $default, $interactive);
    }

    private static function isAssociativeArray(array $array): bool
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }
}
