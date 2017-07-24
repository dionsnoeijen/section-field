<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityPrePersistGenerator implements Generator
{

    public static function generate(Field $field): Template
    {
        if (in_array('prePersist', $field->getConfig()->getEntityEvents())) {
            $asString = (string)TemplateLoader::load(FullyQualifiedClassNameConverter::toDir(
                    $field->getFieldType()->getFullyQualifiedClassName()
                ) . '/GeneratorTemplate/prepersist.php.template'
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

        throw new NoPrePersistEntityEventDefinedInFieldConfigException();
    }
}
