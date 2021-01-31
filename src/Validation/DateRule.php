<?php declare(strict_types = 1);

namespace Vinnia\Util\Validation;

use DateTimeImmutable;

class DateRule extends Rule
{
    /**
     * @inheritDoc
     */
    protected function validateValue($value, array $params = []): bool
    {
        return DateTimeImmutable::createFromFormat($params[0] ?? 'Y-m-d', $value) !== false;
    }
}
