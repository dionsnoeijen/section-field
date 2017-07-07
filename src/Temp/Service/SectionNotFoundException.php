<?php

namespace Tardigrades\SectionField\Service;

class SectionNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = !empty($message) ? $message : 'Section not found';

        parent::__construct($message, $code, $previous);
    }
}
