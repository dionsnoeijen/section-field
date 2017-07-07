<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Throwable;

class NoFieldNameDefinedException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message = empty($message) ? 'It appears there are no (is no) field name(s) defined, this is required. Add an array under fields with as key the i18n identifier and as value the name in the correct translation.' : $message;

        parent::__construct($message, $code, $previous);
    }
}
