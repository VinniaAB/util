<?php
declare(strict_types = 1);

namespace Vinnia\Util\Text;

class Template
{
    /**
     * @var string
     */
    private $file;

    /**
     * @param string $file
     */
    function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Render the template to a string.
     * @param  array $data data to inject into the template
     * @return string rendered template
     */
    public function render(array $data = []): string
    {
        ob_start();
        extract($data, EXTR_SKIP);
        require $this->file;
        return ob_get_clean();
    }

    /**
     * Helper function to enable template nesting
     * @param  string $file path
     * @param  array $data data to inject
     * @return string rendered template
     */
    public function nest(string $file, array $data = []): string
    {
        return (new self($file))->render($data);
    }
}
