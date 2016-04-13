<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2016-04-14
 * Time: 01:14
 */

namespace Vinnia\Util\Tests;


use Vinnia\Util\Template;

class TemplateTest extends AbstractTest
{

    public function testRender()
    {
        $t = new Template(__DIR__ . '/../_data/template.php');
        $str = $t->render(['message' => 'Hello World']);
        
        $this->assertEquals('<div>Hello World</div>', $str);
    }

    public function testRenderNested()
    {
        $t = new Template(__DIR__ . '/../_data/nested-template.php');
        $str = $t->render(['message' => 'Hello World']);

        $this->assertEquals('<div>Hello World</div>', $str);
    }
    
}
