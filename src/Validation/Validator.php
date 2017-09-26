<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 15:01
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;

class Validator
{

    /**
     * @var RuleInterface[]
     */
    private $builtins;

    /**
     * @var RuleInterface[][]
     */
    private $ruleSet;

    /**
     * Validator constructor.
     * @param string[] $rules
     */
    function __construct(array $rules)
    {
        $this->builtins = [
            'required' => new RequiredRule('The "%s" property is required'),
            'nullable' => new CallableRule('is_null', 'The "%s" property must be null', 90, true, true),
            'integer' => new CallableRule('is_int', 'The "%s" property must be an integer'),
            'string' => new CallableRule('is_string', 'The "%s" property must be a string'),
            'numeric' => new CallableRule('is_numeric', 'The "%s" property must be numeric'),
            'array' => new CallableRule('is_array', 'The "%s" property must be an array'),
            'boolean' => new CallableRule('is_bool', 'The "%s" property must be a boolean'),
            'float' => new CallableRule('is_float', 'The "%s" property must be a float'),
            'eq' => function (string $key, string $rule, array $params) {
                return new CallableRule(
                    function ($value) use ($params) {
                        return $value == $params[0];
                    },
                    "The \"%s\" property must be equal to \"{$params[0]}\""
                );
            },
            'ne' => function (string $key, string $rule, array $params) {
                return new CallableRule(
                    function ($value) use ($params) {
                        return $value != $params[0];
                    },
                    "The \"%s\" property cannot be equal to \"{$params[0]}\""
                );
            },
            'in' => function (string $key, string $rule, array $params) {
                $values = implode(',', $params);
                return new CallableRule(
                    function ($value) use ($params) {
                        return in_array($value, $params);
                    },
                    "The \"%s\" property must be in \"{$values}\""
                );
            },
        ];

        $this->builtins['str'] = $this->builtins['string'];
        $this->builtins['int'] = $this->builtins['integer'];
        $this->builtins['bool'] = $this->builtins['boolean'];

        $this->ruleSet = $this->createRuleSet($rules);
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function createRuleSet(array $rules): array
    {
        $ruleSet = [];
        foreach ($rules as $key => $rule) {
            $ruleSet[$key] = [];

            // rules are separated by "|"
            $exploded = explode('|', $rule);
            $instances = [];
            foreach ($exploded as $item) {

                // params are separated by ":"
                $parts = explode(':', $item);
                $ruleName = $parts[0];
                $params = array_slice($parts, 1);

                if (isset($this->builtins[$ruleName])) {
                    $builtin = $this->builtins[$ruleName];
                    $instances[] = is_callable($builtin) ?
                        $builtin($key, $ruleName, $params) :
                        $this->builtins[$ruleName];
                }
            }

            usort($instances, function (RuleInterface $a, RuleInterface $b): int {
                return $a->getPriority() <=> $b->getPriority();
            });

            $ruleSet[$key] = $instances;
        }
        return $ruleSet;
    }

    /**
     * @param array $data
     * @return ErrorBag
     */
    public function validate(array $data): ErrorBag
    {
        $dataSet = new DataSet($data);
        $bag = new ErrorBag();

        foreach ($this->ruleSet as $key => $rules) {
            foreach ($rules as $rule) {
                $e = $rule->validateRuleKey($dataSet, $key);

                if ($e->count() === 0 && $rule->shouldBreakRuleChain()) {
                    break;
                }

                if (!$rule->isOptional()) {
                    $bag = $bag->mergeWith($e);
                }
            }
        }

        return $bag;
    }

}
