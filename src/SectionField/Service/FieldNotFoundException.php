<?php

namespace Tardigrades\SectionField\Service;

class FieldNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = !empty($message) ? $message : 'Field not found';

        parent::__construct($message, $code, $previous);
    }
}
