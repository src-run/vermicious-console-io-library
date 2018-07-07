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
use SR\Console\Input\Component\Question\Answer\ScalarAnswer;
use SR\Console\Output\Exception\InvalidArgumentException;
use SR\Console\Output\Exception\RuntimeException;
use SR\Console\Output\Style\StyleAwareInternalTrait;
use SR\Console\Output\Style\StyleInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\QuestionHelper as BaseQuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class QuestionHelper extends BaseQuestionHelper
{
    use StyleAwareInternalTrait;

    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @param StyleInterface $style
     */
    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);

        try {
            $this->reflection = new \ReflectionClass(BaseQuestionHelper::class);
        } catch (\ReflectionException $exception) {
            $this->reflection = new \ReflectionObject($this);
        }
    }

    /**
     * @deprecated use {@see askQuestion} instead
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Question        $question
     *
     * @return mixed
     */
    public function ask(InputInterface $input, OutputInterface $output, Question $question)
    {
        return $this->createStyleNewInputOutput($input, $output)->handleQuestion(new ConfirmationQuestion($question));
    }

    /**
     * @param string        $question
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $normalizer
     *
     * @return AnswerInterface
     */
    public function question(string $question, string $default = null, \Closure $validator = null, \Closure $normalizer = null): AnswerInterface
    {
        return $this->handleQuestion(new Question($question, $default), $validator, $normalizer);
    }

    /**
     * @param string        $question
     * @param string|null   $default
     * @param \Closure|null $validator
     * @param \Closure|null $normalizer
     *
     * @return AnswerInterface
     */
    public function hiddenQuestion(string $question, string $default = null, \Closure $validator = null, \Closure $normalizer = null): AnswerInterface
    {
        return $this->handleQuestion(new Question($question, $default), $validator, $normalizer, function (Question $question) {
            $question->setHidden(true);
        });
    }

    /**
     * @param string        $question
     * @param bool          $default
     * @param \Closure|null $validator
     * @param \Closure|null $normalizer
     *
     * @return BooleanAnswer|AnswerInterface
     */
    public function confirm(string $question, bool $default = true, \Closure $validator = null, \Closure $normalizer = null): BooleanAnswer
    {
        return $this->handleQuestion(new ConfirmationQuestion($question, $default), $validator, $normalizer);
    }

    /**
     * @param string        $question
     * @param array         $choices
     * @param string|null   $default
     * @param bool          $multiSelect
     * @param \Closure|null $validator
     * @param \Closure|null $normalizer
     *
     * @return AnswerInterface
     */
    public function choice(string $question, array $choices, string $default = null, bool $multiSelect = false, \Closure $validator = null, \Closure $normalizer = null): AnswerInterface
    {
        if (null !== $default) {
            $default = array_flip($choices)[$default] ?? null;
        }

        return $this->handleQuestion(
            (new ChoiceQuestion($question, $choices, $default))->setMultiselect($multiSelect),
            $validator,
            $normalizer
        );
    }

    /**
     * @param string        $question
     * @param array         $choices
     * @param string|null   $default
     * @param bool          $multiSelect
     * @param \Closure|null $validator
     * @param \Closure|null $normalizer
     *
     * @return AnswerInterface
     */
    public function hiddenChoice(string $question, array $choices, string $default = null, bool $multiSelect = false, \Closure $validator = null, \Closure $normalizer = null): AnswerInterface
    {
        if (null !== $default) {
            $default = array_flip($choices)[$default];
        }

        return $this->handleQuestion(
            (new ChoiceQuestion($question, $choices, $default))->setMultiselect($multiSelect),
            $validator,
            $normalizer,
            function (ChoiceQuestion $question): void {
                $question->setHidden(true);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function writeError(OutputInterface $output, \Exception $exception = null)
    {
        $this->style->newline();
        $this->style->error($exception->getMessage());
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
     * @param Question      $question
     * @param \Closure|null $validator
     * @param \Closure|null $normalizer
     * @param \Closure|null $configurator
     *
     * @return AnswerInterface
     */
    private function handleQuestion(Question $question, \Closure $validator = null, \Closure $normalizer = null, \Closure $configurator = null): AnswerInterface
    {
        $this->configureQuestion($question, $validator, $normalizer, $configurator);
        $this->writePriorQuestionOutput();

        try {
            $answer = $this->interviewQuestion($question);
        } catch (\Exception $exception) {
            $this->writeError($this->getOutput(), $exception);
        }

        $this->writeAfterQuestionOutput();

        return $answer ?? self::createAnswer($question, null, false, true);
    }

    /**
     * @param Question $question
     *
     * @throws \Exception
     *
     * @return AnswerInterface
     */
    private function interviewQuestion(Question $question): AnswerInterface
    {
        return $this->getInput()->isInteractive()
            ? $this->interviewQuestionAsInteractiveValidated($question)
            : $this->interviewQuestionNoInteractive($question);
    }

    /**
     * @param Question $question
     *
     * @return AnswerInterface
     */
    private function interviewQuestionAsInteractiveValidated(Question $question): AnswerInterface
    {
        $i = $this->getInput();
        $o = $this->getOutput();

        if ($o instanceof ConsoleOutputInterface) {
            $o = $o->getErrorOutput();
        }

        if ($i instanceof StreamableInputInterface && $stream = $i->getStream()) {
            $this->setInputStream($stream);
        }

        $priorError = null;
        $iterations = $question->getMaxAttempts();

        while (null === $iterations || $iterations--) {
            if (null !== $priorError) {
                $this->writeError($o, $priorError);
            }

            try {
                return ($question->getValidator())(
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

    /**
     * @param Question $question
     *
     * @return AnswerInterface
     */
    private function interviewQuestionNoInteractive(Question $question): AnswerInterface
    {
        if ($question instanceof ChoiceQuestion) {
            $choices = $question->getChoices();

            if (isset($choices[$question->getDefault()])) {
                return self::createAnswer($question, $choices[$question->getDefault()], true, false);
            }

            throw new RuntimeException(
                'Configured default "%s" is not an available choice.', $question->getDefault()
            );
        }

        return self::createAnswer($question, $question->getDefault(), true, false);
    }

    /**
     * @param Question        $question
     * @param OutputInterface $o
     *
     * @return ScalarAnswer
     */
    private function interviewQuestionAsInteractive(Question $question, OutputInterface $o): AnswerInterface
    {
        $this->writePrompt($o, $question);

        $isDefault = false;
        $userInput = null === $question->getAutocompleterValues() || false === $this->invokePrivateMethod('hasSttyAvailable')
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
     * @param Question        $question
     * @param OutputInterface $o
     *
     * @return mixed
     */
    private function readResponseUseAutoComplete(Question $question, OutputInterface $o)
    {
        $values = $question->getAutocompleterValues();

        if ($values instanceof \Traversable) {
            $values = iterator_to_array($values);
        }

        return $this->invokePrivateMethod('autocomplete', [$o, $question, $this->getInputStream(), $values]);
    }

    /**
     * @param Question        $question
     * @param OutputInterface $o
     *
     * @return mixed
     */
    private function readResponseNonAutoComplete(Question $question, OutputInterface $o)
    {
        if (false === $response = ($question->isHidden() ? $this->readResponseHidden($question, $o) : false)) {
            $response = $this->readResponseInputs();
        }

        return $response;
    }

    /**
     * @param Question        $question
     * @param OutputInterface $o
     *
     * @return string
     */
    private function readResponseHidden(Question $question, OutputInterface $o): string
    {
        try {
            $response = $this->invokePrivateMethod('getHiddenResponse', [$o, $this->getInputStream()]);
        } catch (RuntimeException $e) {
            if (!$question->isHiddenFallback()) {
                throw $e;
            }
        }

        return $response ?? '';
    }

    /**
     * @return string
     */
    private function readResponseInputs(): string
    {
        $response = fgets($this->getInputStream(), 4096);

        if (false === $response) {
            throw new RuntimeException('Aborted');
        }

        return $response;
    }

    /**
     * @param Question      $question
     * @param \Closure|null $validator
     * @param \Closure|null $normalizer
     * @param \Closure|null $configurator
     */
    private function configureQuestion(Question $question, \Closure $validator = null, \Closure $normalizer = null, \Closure $configurator = null): void
    {
        if (null !== $validator) {
            $question->setValidator($validator);
        }

        if ($question instanceof ChoiceQuestion) {
            $question->setValidator(function ($result) use ($question, $validator): AnswerInterface {
                return ($validator ?? function ($answer) { return $answer; })(
                    $this->validateChoiceResult($question, $result)
                );
            });
        }

        if (null === $question->getValidator()) {
            $question->setValidator(function (AnswerInterface $answer): AnswerInterface {
                return $answer;
            });
        }

        if (null !== $normalizer) {
            $question->setNormalizer($normalizer);
        }

        if (null !== $configurator) {
            $configurator($question);
        }
    }

    private function validateChoiceResult(ChoiceQuestion $question, AnswerInterface $answer)
    {
        $multiSelects = $this->resolvePrivateProperty('multiselect', null, $question);
        $choiceValues = $question->getChoices();
        $inputChoices = true === $multiSelects && 1 === preg_match('/^[^,]+(?:,[^,]+)*$/', $answer->stringifyAnswer(), $matches)
            ? explode(',', preg_replace('{,\s+}', ',', $answer->stringifyAnswer()))
            : [$answer->stringifyAnswer()];

        $multiSelectChoices = [];

        foreach ($inputChoices as $value) {
            $choices = [];

            foreach ($choiceValues as $choiceIndex => $choiceValue) {
                if ($choiceValue === $value) {
                    $choices[] = $choiceIndex;
                }
            }

            if (count($choices) > 1) {
                throw new InvalidArgumentException(
                    'The provided answer is ambiguous. Value should be one of %s.', implode(' or ', $choices)
                );
            }

            $choice = array_search($value, $choiceValues, true);

            if (!self::isAssociativeArray($choiceValues)) {
                if (false !== $choice) {
                    $choice = $choiceValues[$choice];
                } elseif (isset($choiceValues[$value])) {
                    $choice = $choiceValues[$value];
                }
            } elseif (false === $choice && isset($choiceValues[$value])) {
                $choice = $value;
            }

            if (false === $choice) {
                if (empty($value)) {
                    throw new InvalidArgumentException(
                        'Invalid empty choice answer provided. Available choices: %s.', self::stringifyChoices($choiceValues)
                    );
                }
                throw new InvalidArgumentException(
                    'Invalid choice answer "%s" provided. Available choices: %s.', $value, self::stringifyChoices($choiceValues)
                );
            }

            $multiSelectChoices[] = (string) $choice;
        }

        return true === $multiSelects
            ? new MultipleChoiceAnswer($question, $multiSelectChoices, $answer->isDefault(), $answer->isInteractive())
            : new ChoiceAnswer($question, current($multiSelectChoices), $answer->isDefault(), $answer->isInteractive());
    }

    /**
     * @param array $choices
     *
     * @return string
     */
    private static function stringifyChoices(array $choices): string
    {
        $string = implode(', ', array_map(function ($value) use ($choices): string {
            return sprintf('"%s" or "%s"', $value, $choices[$value]);
        }, array_keys($choices)));

        return count(explode(', ', $string)) > 2
            ? preg_replace('{(.+".+?"), (.+?)$}', '\1, and \2', $string)
            : preg_replace('{(.+".+?"), (.+?)$}', '\1 and \2', $string);
    }

    /**
     * @param Question        $question
     * @param OutputInterface $o
     */
    private function writeQuestionText(Question $question, OutputInterface $o): void
    {
        $o->writeln(self::formatQuestionPromptText($question, function (string $text, ...$replacements): string {
            return vsprintf($text, array_map(function ($question) {
                return $question instanceof Question
                    ? OutputFormatter::escapeTrailingBackslash($question->getQuestion())
                    : OutputFormatter::escape($question);
            }, $replacements));
        }));
    }

    /**
     * @param Question $question
     * @param \Closure $formatter
     *
     * @return string
     */
    private static function formatQuestionPromptText(Question $question, \Closure $formatter): string
    {
        if (null === $question->getDefault()) {
            return $formatter(' <info>%s</info>:', $question);
        }

        if ($question instanceof ConfirmationQuestion) {
            return $formatter(' <info>%s (yes/no)</info> [<comment>%s</comment>]:', $question, $question->getDefault() ? 'yes' : 'no');
        }

        if ($question instanceof ChoiceQuestion && $question->isMultiselect()) {
            return $formatter(' <info>%s</info> [<comment>%s</comment>]:', $question, implode(', ', array_map(function ($value) use ($question) {
                return $question->getChoices()[trim($value)];
            }, explode(',', $question->getDefault()))));
        }

        if ($question instanceof ChoiceQuestion) {
            return $formatter(' <info>%s</info> [<comment>%s</comment>]:', $question, $question->getChoices()[$question->getDefault()]);
        }

        return $formatter(' <info>%s</info> [<comment>%s</comment>]:', $question, $question->getDefault());
    }

    /**
     * @param ChoiceQuestion  $question
     * @param OutputInterface $o
     */
    private function writeQuestionChoices(ChoiceQuestion $question, OutputInterface $o): void
    {
        foreach ($question->getChoices() as $key => $value) {
            $o->writeln(sprintf(sprintf(
                '  [<comment>%%-%ds</comment>] %%s', max(array_map('strlen', array_keys($question->getChoices())))
            ), $key, $value));
        }
    }

    /**
     * Write pre-question output (handle prepending block).
     */
    private function writePriorQuestionOutput(): void
    {
        if ($this->getInput()->isInteractive()) {
            $this->style->prependBlock();
        }
    }

    /**
     * Write post-question output (handle final newlines)
     */
    private function writeAfterQuestionOutput(): void
    {
        if ($this->getInput()->isInteractive()) {
            $this->style->newLine();
        }
    }

    /**
     * @return OutputInterface
     */
    private function getOutput(): OutputInterface
    {
        return $this->style->getOutput();
    }

    /**
     * @return InputInterface
     */
    private function getInput(): InputInterface
    {
        return $this->style->getInput();
    }

    /**
     * @param resource $stream
     */
    private function setInputStream($stream): void
    {
        $this->assignPrivateProperty('inputStream', $stream);
    }

    /**
     * @return resource
     */
    private function getInputStream()
    {
        return $this->resolvePrivateProperty('inputStream', STDIN);
    }

    /**
     * @param null $object
     *
     * @return \ReflectionClass
     */
    private function resolveReflectionObject($object = null): \ReflectionClass
    {
        return null === $object ? $this->reflection : new \ReflectionObject($object);
    }

    /**
     * @param string      $name
     * @param mixed[]     ...$arguments
     * @param object|null $object
     *
     * @return mixed
     */
    private function invokePrivateMethod(string $name, array $arguments = [], $object = null)
    {
        $method = $this->resolveReflectionObject($object)->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($object ?? $this, $arguments);
    }

    /**
     * @param string      $name
     * @param mixed       $value
     * @param object|null $object
     */
    private function assignPrivateProperty(string $name, $value, $object = null): void
    {
        $property = $this->resolveReflectionObject($object)->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object ?? $this, $value);
    }

    /**
     * @param string      $name
     * @param mixed|null  $default
     * @param object|null $object
     *
     * @return mixed|null
     */
    private function resolvePrivateProperty(string $name, $default = null, $object = null)
    {
        $property = $this->resolveReflectionObject($object)->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($object ?? $this) ?? $default;
    }

    /**
     * @param Question   $question
     * @param mixed|null $response
     * @param bool       $default
     * @param bool       $interactive
     *
     * @return AnswerInterface
     */
    private static function createAnswer(Question $question, $response = null, bool $default = false, bool $interactive = true): AnswerInterface
    {
        return $question instanceof ConfirmationQuestion
            ? new BooleanAnswer($question, $response, $default, $interactive)
            : new ScalarAnswer($question, $response, $default, $interactive);
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private static function isAssociativeArray(array $array): bool
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }
}
