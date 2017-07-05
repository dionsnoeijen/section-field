<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

class FieldTypeNotFoundException extends \Exception
{
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        $message = !empty($message) ? $message : "Field type not found, install the accompanying field type first.";

        parent::__construct($message, $code, $previous);
    }
}
