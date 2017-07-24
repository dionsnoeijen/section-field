<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Throwable;

class NoPreUpdateEntityEventDefinedInFieldConfigException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message = empty($message) ? 'In the field config this key: entityEvents with this value: - preUpdate is not defined. Skipping pre update rendering for this field.': $message;

        parent::__construct($message, $code, $previous);
    }
}
