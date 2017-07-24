<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Throwable;

class NoPrePersistEntityEventDefinedInFieldConfigException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message = empty($message) ? 'In the field config this key: entityEvents with this value: - prePersist is not defined. Skipping pre update rendering for this field.': $message;

        parent::__construct($message, $code, $previous);
    }
}
