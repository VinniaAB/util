<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2019-02-02
 * Time: 03:04
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;

use DateTimeImmutable;

class DateRule extends Rule
{

    /**
     * @var string
     */
    protected $format;

    /**
     * DateRule constructor.
     * @param string $format
     */
    function __construct(string $format)
    {
        $this->format = $format;

        parent::__construct("The \"%s\" property must be a date of format \"$format\"", 100, false, true);
    }

    /**
     * @inheritDoc
     */
    protected function validateValue($value, array $params = []): bool
    {
        return DateTimeImmutable::createFromFormat($this->format, $value) !== false;
    }

}
