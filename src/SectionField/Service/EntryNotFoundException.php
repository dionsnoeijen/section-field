<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Throwable;

class EntryNotFoundException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message = empty($message) ? 'Entry not found' : $message;

        parent::__construct($message, $code, $previous);
    }
}
