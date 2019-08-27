<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 15:04
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;

use RuntimeException;
use Vinnia\Util\Text\TemplateString;

/**
 * Class RequiredRule
 * @package Vinnia\Util\Validation
 */
class RequiredRule implements RuleInterface
{
    /**
     * @var string
     */
    private $errorMessage;

    /**
     * RequiredRule constructor.
     * @param string $errorMessage
     */
    function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @inheritDoc
     */
    public function validate(DataSet $dataSet, string $ruleKey, ?string $expandedKey, array $params = []): ErrorBag
    {
        $parents = $dataSet->getParentElements($ruleKey);

        // extract the last part of the rule key. this is
        // usually everything after the last "." or the whole
        // key if there is no dot.
        if (preg_match('/\.?([^\.]+)$/', $ruleKey, $matches) !== 1) {
            throw new RuntimeException("Could not find end of key \"$ruleKey\"");
        }

        $endOfKey = $matches[1];
        $bag = new ErrorBag();

        // when we have all parent elements we can compare
        // them against the last part of the rule key. if
        // there are no
        foreach ($parents as $parentKey => $element) {
            $childSet = new DataSet($element);
            $keys = $childSet->getMatchingKeys($endOfKey);

            if (empty($keys)) {
                $fullKey = implode('.', array_filter([$parentKey, $endOfKey], function (string $key) {
                    // the only time when we don't want to
                    // include the parent key in the path is
                    // when the parent is the root data set.
                    return $key !== DataSet::PARENT_KEY_ROOT;
                }));
                $bag->addError($fullKey, (new TemplateString($this->errorMessage))->render([
                    'property' => $fullKey,
                ]));
            }
        }

        return $bag;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 10;
    }

    /**
     * This rule can only succeed and will not generate any errors if it fails.
     * @return bool
     */
    public function yieldsErrors(): bool
    {
        return true;
    }

    /**
     * If this rule is valid we stop the validation here.
     * @return bool
     */
    public function breaksRuleChainOnSuccess(): bool
    {
        return false;
    }
}
