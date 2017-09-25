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

}