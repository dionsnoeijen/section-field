<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

class ApplicationNotFoundException extends \Exception
{
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        $message = !empty($message) ? $message : 'Application not found';

        parent::__construct($message, $code, $previous);
    }
}
