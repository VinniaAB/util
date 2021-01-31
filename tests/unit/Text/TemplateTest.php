<?php declare(strict_types = 1);

namespace Vinnia\Util\Tests\Text;

use Vinnia\Util\Tests\AbstractTest;
use Vinnia\Util\Text\Template;

class TemplateTest extends AbstractTest
{
    public function testRender()
    {
        $t = new Template(__DIR__ . '/../../_data/template.php');
        $str = $t->render(['message' => 'Hello World']);
        
        $this->assertEquals('<div>Hello World</div>', $str);
    }

    public function testRenderNested()
    {
        $t = new Template(__DIR__ . '/../../_data/nested-template.php');
        $str = $t->render(['message' => 'Hello World']);

        $this->assertEquals('<div>Hello World</div>', $str);
    }
}
