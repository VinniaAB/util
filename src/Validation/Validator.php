<?php declare(strict_types = 1);

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
            'required' => new RequiredRule('The "{{property}}" property is required'),
            'nullable' => new CallableRule('is_null', 'The "{{property}}" property must be null', 90, true, false),
            'integer' => new CallableRule('is_int', 'The "{{property}}" property must be an integer'),
            'string' => new CallableRule('is_string', 'The "{{property}}" property must be a string'),
            'numeric' => new CallableRule('is_numeric', 'The "{{property}}" property must be numeric'),
            'array' => new CallableRule('is_array', 'The "{{property}}" property must be an array'),
            'boolean' => new CallableRule('is_bool', 'The "{{property}}" property must be a boolean'),
            'float' => new CallableRule('is_float', 'The "{{property}}" property must be a float'),
            'eq' => new CallableRule(function ($value, array $params = []) {
                return $value == $params[0];
            }, 'The "{{property}}" property must be equal to "{{param_0}}"'),
            'ne' => new CallableRule(function ($value, array $params = []) {
                return $value != $params[0];
            }, 'The "{{property}}" property must not be equal to "{{param_0}}"'),

            // TODO: fix the error message of this rule. needs
            // something a little bit more dynamic.
            'in' => new CallableRule(function ($value, array $params = []) {
                return in_array($value, $params);
            }, 'The "{{property}}" property is not in the allowed set of values'),
            'min' => new BoundParametersRule(
                new SizeRule('The "{{property}}" property must be greater than or equal to "{{param_1}}"'),
                [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL]
            ),
            'max' => new BoundParametersRule(
                new SizeRule('The "{{property}}" property must be less than or equal to "{{param_1}}"'),
                [SizeRule::COMPARE_LESS_THAN_OR_EQUAL]
            ),
            'date_format' => new DateRule('The "{{property}}" property must be a date of format "{{param_0}}"'),
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
                $parts = preg_split('/(?<!\\\\):/', $item);
                $ruleName = $parts[0];
                $params = array_slice($parts, 1);

                if (isset($this->builtins[$ruleName])) {
                    $instances[] = new BoundParametersRule($this->builtins[$ruleName], $params);
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
            $properties = $dataSet->getMatchingKeys($key);

            // sometimes we don't find any matching properties
            // but we still want to execute the rules on the
            // whole rule key. this is useful for the "required"
            // rule for example.
            if (empty($properties)) {
                $properties = [null];
            }

            foreach ($properties as $property) {
                foreach ($rules as $rule) {
                    $e = $rule->validate($dataSet, $key, $property);

                    if ($e->count() > 0 && $rule->yieldsErrors()) {
                        $bag = $bag->mergeWith($e);
                    }

                    if ($e->count() == 0 && $rule->breaksRuleChainOnSuccess()) {
                        break;
                    }
                }
            }
        }

        return $bag;
    }
}
