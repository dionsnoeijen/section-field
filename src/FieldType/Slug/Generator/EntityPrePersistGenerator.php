<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Slug\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\FieldType\Slug\ValueObject\Slug as SlugValueObject;

class EntityPrePersistGenerator implements Generator
{
    public static function generate(Field $field, ...$managers): Template
    {
        $template = PrePersistTemplate::create(
            TemplateLoader::load(
                __DIR__ . '/../GeneratorTemplate/prepersist.php.template'
            )
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ propertyName }}',
            $field->getConfig()->getPropertyName(),
            $asString
        );
        $asString = str_replace(
            '{{ assignment }}',
            self::makeSlugAssignment(
                SlugValueObject::create(
                    $field->getConfig()->getTypeConfig()
                )
            ),
            $asString
        );

        return Template::create($asString);
    }

    private static function makeSlugAssignment(SlugValueObject $slug): string
    {
        $assignment = [];
        foreach ($slug->toArray() as $element) {
            $element = explode('|', $element);
            $attach = '';
            if (count($element) > 1) {
                switch ($element[1]) {
                    case 'DateTime':
                        $attach = '->format(\'' . $element[2] . '\')';
                        break;
                }
            }
            $assignment[] = '$this->get' . ucfirst(StringConverter::toCamelCase($element[0])) . '()' . $attach;
        }
        return 'Tardigrades\Helper\StringConverter::toSlug(' . implode(' . \'-\' . ', $assignment) . ');';
    }
}
