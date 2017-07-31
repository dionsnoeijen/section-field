<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityPreUpdateGenerator implements Generator
{

    public static function generate(Field $field): Template
    {
        if (in_array('preUpdate', $field->getConfig()->getEntityEvents())) {

            $asString = (string)TemplateLoader::load(FullyQualifiedClassNameConverter::toDir(
                    $field->getFieldType()->getFullyQualifiedClassName()
                ) . '/GeneratorTemplate/preupdate.php.template'
            );

            $asString = str_replace(
                '{{ propertyName }}',
                $field->getConfig()->getPropertyName(),
                $asString
            );

            return Template::create(
                $asString
            );
        }

        throw new NoPreUpdateEntityEventDefinedInFieldConfigException();
    }
}
