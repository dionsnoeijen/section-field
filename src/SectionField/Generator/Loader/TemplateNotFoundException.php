<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator\Loader;

use Throwable;

class TemplateNotFoundException extends \InvalidArgumentException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message = empty($message) ? 'Template not found' : $message;
        parent::__construct($message, $code, $previous);
    }
}
