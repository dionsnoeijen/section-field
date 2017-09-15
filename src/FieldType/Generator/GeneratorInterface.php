<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\ValueObject\Template;

interface GeneratorInterface
{
    public static function generate(FieldInterface $field): Template;
}
