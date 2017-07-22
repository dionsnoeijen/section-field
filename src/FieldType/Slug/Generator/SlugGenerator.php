<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Slug\Generator;

use Tardigrades\FieldType\FieldTypeInterface\EntityPrePersistGenerator;
use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\FieldType\Slug\ValueObject\Slug as SlugValueObject;

class SlugGenerator implements EntityPrePersistGenerator
{
    public function renderPrePersist(FieldConfig $fieldConfig): PrePersistTemplate
    {
        $template = PrePersistTemplate::create(
            TemplateLoader::load(__DIR__ . '/../GeneratorTemplate/prepersist.php.template')
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ propertyName }}',
            $fieldConfig->getPropertyName(),
            $asString
        );
        $asString = str_replace(
            '{{ assignment }}',
            $this->makeSlugAssignment($this->getTypeConfig($fieldConfig->getTypeConfig())),
            $asString
        );

        return PrePersistTemplate::create($asString);
    }

    protected function makeSlugAssignment(SlugValueObject $slug): string
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

    public function getTypeConfig(array $typeConfig): SlugValueObject
    {
        return SlugValueObject::create($typeConfig);
    }
}
