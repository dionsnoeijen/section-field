<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Throwable;

class FieldTypeDoesNotExistException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message = empty($message) ? 'Field type not found based on fully qualified class name' : $message;

        parent::__construct($message, $code, $previous);
    }
}
