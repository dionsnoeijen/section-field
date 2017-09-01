<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityUseGenerator implements Generator
{

    public static function generate(Field $field): Template
    {
        return Template::create(
            (string) TemplateLoader::load(
               $field->getFieldType()->getInstance()->directory() .
                '/GeneratorTemplate/entity.use.php.template'
            )
        );
    }
}
