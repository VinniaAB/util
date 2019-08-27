<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2018-03-16
 * Time: 14:33
 */
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Text;

use Vinnia\Util\Text\TemplateString;
use Vinnia\Util\Tests\AbstractTest;

class TemplateStringTest extends AbstractTest
{

    public function renderProvider()
    {
        return [
            ['Hello {{name}}', ['name' => 'HELMUT'], 'Hello HELMUT'],
            ['Hello {{name}} {{name}}', ['name' => 'HELMUT'], 'Hello HELMUT HELMUT'],
            ['Hello {{name}}', [], 'Hello {{name}}'],
            ['Hello {{  name   }}', ['name' => 'HELMUT'], 'Hello HELMUT'],
        ];
    }

    /**
     * @dataProvider renderProvider
     * @param string $template
     * @param array $data
     * @param string $expected
     */
    public function testRender(string $template, array $data, string $expected)
    {
        $tpl = new TemplateString($template);
        $this->assertEquals($expected, $tpl->render($data));
    }

    public function testRenderWithCustomDelimiters()
    {
        $tpl = new TemplateString('Hello %name%', ['%', '%']);
        $this->assertEquals('Hello World', $tpl->render(['name' => 'World']));
    }

}
