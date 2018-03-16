<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 15:23
 */
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;


use Vinnia\Util\Tests\AbstractTest;
use Vinnia\Util\Validation\Validator;

class ValidatorTest extends AbstractTest
{

    public function testWorksWithStringRules()
    {
        $validator = new Validator([
            'prop' => 'required',
        ]);

        $errors = $validator->validate([
            'hello' => 'world',
        ]);

        $this->assertCount(1, $errors);
        $this->assertEquals('The "prop" property is required', $errors->getErrors()['prop'][0]);
    }

    public function testCombinesMultipleStringRulesWithOneFailing()
    {
        $validator = new Validator([
            'prop' => 'integer|required',
        ]);

        $errors = $validator->validate([
            'prop' => 'one',
        ]);

        $this->assertCount(1, $errors);
        $this->assertEquals('The "prop" property must be an integer', $errors->getErrors()['prop'][0]);
    }

    public function testCombinesMultipleStringRules()
    {
        $validator = new Validator([
            'prop' => 'required|integer',
        ]);

        $errors = $validator->validate([
            'prop' => 1,
        ]);

        $this->assertCount(0, $errors);
    }

    public function testOptionalRulesDoesNotGenerateMessages()
    {
        $validator = new Validator([
            'prop' => 'integer|nullable',
        ]);

        $bag = $validator->validate([
            'prop' => '1',
        ]);

        $this->assertCount(1, $bag);
        $this->assertEquals('The "prop" property must be an integer', $bag->getErrors()['prop'][0]);
    }

    public function testBreaksChain()
    {
        $validator = new Validator([
            'prop' => 'integer|nullable',
        ]);

        $bag = $validator->validate([
            'prop' => null,
        ]);

        $this->assertCount(0, $bag);
    }

    public function testEqualRule()
    {
        $validator = new Validator([
            'prop' => 'eq:one',
        ]);

        $bag = $validator->validate([
            'prop' => null,
        ]);

        $this->assertCount(1, $bag);

        $bag = $validator->validate([
            'prop' => 'one',
        ]);

        $this->assertCount(0, $bag);
    }

    public function testNotEqualRule()
    {
        $validator = new Validator([
            'prop' => 'ne:one',
        ]);
        $bag = $validator->validate([
            'prop' => 'one',
        ]);
        $this->assertCount(1, $bag);
        $bag = $validator->validate([
            'prop' => 'two',
        ]);
        $this->assertCount(0, $bag);
    }

    public function testInRule()
    {
        $validator = new Validator([
            'prop' => 'in:1:2:3',
        ]);
        $bag = $validator->validate([
            'prop' => 4
        ]);
        $this->assertCount(1, $bag);
        $bag = $validator->validate([
            'prop' => 3
        ]);
        $this->assertCount(0, $bag);
    }

    public function testAddsErrorsForMultipleMatchingProperties()
    {
        $validator = new Validator([
            'prop.*' => 'integer|nullable',
        ]);

        $bag = $validator->validate([
            'prop' => [null, 2, '3', 'four'],
        ]);

        $this->assertCount(1, $bag->getErrors()['prop.2']);
        $this->assertCount(1, $bag->getErrors()['prop.3']);
    }

    public function testRecognizesParentKeysOfDeepArrays()
    {
        $validator = new Validator([
            'prop' => 'required|array',
            'prop.*' => 'integer',
        ]);

        $bag = $validator->validate([
            'prop' => [1, 2, 3],
        ]);

        $this->assertCount(0, $bag->getErrors());
    }

}