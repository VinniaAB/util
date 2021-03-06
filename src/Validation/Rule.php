<?php declare(strict_types = 1);

namespace Vinnia\Util\Validation;

use Vinnia\Util\Arrays;
use Vinnia\Util\Text\TemplateString;

abstract class Rule implements RuleInterface
{
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var bool
     */
    protected $breaksRuleChainOnSuccess;

    /**
     * @var bool
     */
    protected $yieldsErrors;

    /**
     * Rule constructor.
     * @param string $errorMessage
     * @param int $priority
     * @param bool $breaksRuleChainOnSuccess
     * @param bool $yieldsErrors
     */
    function __construct(
        string $errorMessage,
        int $priority = 100,
        bool $breaksRuleChainOnSuccess = false,
        bool $yieldsErrors = true
    ) {
        $this->errorMessage = $errorMessage;
        $this->priority = $priority;
        $this->breaksRuleChainOnSuccess = $breaksRuleChainOnSuccess;
        $this->yieldsErrors = $yieldsErrors;
    }

    /**
     * @param mixed $value
     * @param string[] $params
     * @return bool
     */
    abstract protected function validateValue($value, array $params = []): bool;

    /**
     * @param string $prop
     * @param array $params
     * @return string
     */
    protected function getErrorMessage(string $prop, array $params = []): string
    {
        $prefixed = array_reduce(array_keys($params), function (array $carry, $key) use ($params) {
            $carry['param_' . $key] = $params[$key];
            return $carry;
        }, []);
        return (new TemplateString($this->errorMessage))->render(array_merge(['property' => $prop], $prefixed));
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return bool
     */
    public function breaksRuleChainOnSuccess(): bool
    {
        return $this->breaksRuleChainOnSuccess;
    }

    /**
     * @return bool
     */
    public function yieldsErrors(): bool
    {
        return $this->yieldsErrors;
    }

    /**
     * @inheritDoc
     */
    public function validate(DataSet $dataSet, string $ruleKey, ?string $expandedKey, array $params = []): ErrorBag
    {
        $errors = new ErrorBag;

        if ($expandedKey === null) {
            return $errors;
        }

        $value = Arrays::get($dataSet->getData(), $expandedKey);

        if (!$this->validateValue($value, $params)) {
            $errors->addError($expandedKey, $this->getErrorMessage($expandedKey, $params));
        }

        return $errors;
    }
}
