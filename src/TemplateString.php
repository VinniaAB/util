<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2018-03-16
 * Time: 14:32
 */
declare(strict_types = 1);

namespace Vinnia\Util;

/**
 * Render a parametrized template string. Example:
 *
 * Hello {{name}} -> Hello World
 *
 * Class TemplateString
 * @package app\notification
 */
class TemplateString
{

    /**
     * @var string
     */
    private $template;

    /**
     * @var string[]
     */
    private $delimiters;

    /**
     * TemplateString constructor.
     * @param string $template
     * @param string[] $delimiters
     */
    function __construct(string $template, array $delimiters = ['{{', '}}'])
    {
        $this->template = $template;
        $this->delimiters = $delimiters;
    }

    /**
     * @param string[] $data
     * @return string
     */
    public function render(array $data = []): string
    {
        $start = preg_quote($this->delimiters[0]);
        $end = preg_quote($this->delimiters[1]);
        $regex = '/' . $start . '\s*(\w+)\s*' . $end . '/';

        return preg_replace_callback($regex, function (array $matches) use ($data) {
            $name = $matches[1];

            // we can't use "isset()" or null coalesce "??"
            // here because they return false on null values.
            return array_key_exists($name, $data) ? (string) $data[$name] : $matches[0];
        }, $this->template);
    }

}
