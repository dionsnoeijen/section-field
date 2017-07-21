<?php

namespace Tardigrades\FieldType\Slug;

use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\Slug\ValueObject\Slug as SlugValueObject;
use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class Slug extends FieldType implements SlugFieldType
{
    public function renderPrePersist(): PrePersistTemplate
    {
        $template = PrePersistTemplate::create(
            TemplateLoader::load(__DIR__ . '/GeneratorTemplate/prepersist.php.template')
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ propertyName }}',
            $this->getConfig()->getPropertyName(),
            $asString
        );
        $asString = str_replace(
            '{{ assignment }}',
            $this->makeSlugAssignment($this->getTypeConfig($this->getConfig()->getTypeConfig())),
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
