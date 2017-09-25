# Validation how-to
The core of the validation lib is `Validator` class.
Instantiate this class with an array of rules. Built-in rules are:

- required
- nullable
- integer
- string
- numeric
- array
- boolean
- float

```php
use Vinnia\Util\Validation\Validator;

$validator = new Validator([
    'name' => 'required|string',
    'friends' => 'required|array',
    'friends.*' => 'required|array',
    'friends.*.name' => 'string',
]);

$data = [
    'name' => 'Helmut',
    'friends' => [
        [
            'name' => 'Dieter',
        ],
        [
            'name' => 'George',
            'phone' => '12345',
        ],
    ],
];


// Validator::validate returns an ErrorBag
$errorBag = $validator->validate($data);

if (count($errorBag) === 0) {
    // valid
}
else {
    // invalid
}

var_dump($errorBag->getErrors());

```
