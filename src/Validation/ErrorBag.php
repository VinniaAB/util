<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 15:07
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;

use Countable;

class ErrorBag implements Countable
{

    /**
     * @var string[][]
     */
    private $errors = [];

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addError(string $property, string $message): void
    {
        if (!isset($this->errors[$property])) {
            $this->errors[$property] = [];
        }

        $this->errors[$property][] = $message;
    }

    /**
     * @param string $property
     * @param string[] $messages
     */
    public function addErrors(string $property, array $messages): void
    {
        foreach ($messages as $message) {
            $this->addError($property, $message);
        }
    }

    /**
     * @param ErrorBag $other
     * @return ErrorBag
     */
    public function mergeWith(ErrorBag $other): ErrorBag
    {
        $bag = new ErrorBag();

        foreach ($this->errors as $property => $messages) {
            $bag->addErrors($property, $messages);
        }

        foreach ($other->errors as $property => $messages) {
            $bag->addErrors($property, $messages);
        }

        return $bag;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->errors);
    }

}
