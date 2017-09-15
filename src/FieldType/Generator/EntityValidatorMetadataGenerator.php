<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityValidatorMetadataGenerator implements GeneratorInterface
{
    public static function generate(FieldInterface $field): Template
    {
        $asString = (string) Template::create(
            (string) TemplateLoader::load(
                $field->getFieldType()->getInstance()->directory() .
                '/GeneratorTemplate/entity.validator-metadata.php.template'
            )
        );

        $asString = str_replace(
            '{{ propertyName }}',
            $field->getConfig()->getPropertyName(),
            $asString
        );

        $generatorConfig = $field->getConfig()->getGeneratorConfig()->toArray();

        foreach ($generatorConfig['entity']['validator'] as $assertion => $assertionOptions) {
            $asString = str_replace(
                '{{ assertion }}',
                $assertion,
                $asString
            );
            $options = '';
            foreach ($assertionOptions as $optionKey => $optionValue) {
                $options .= "'{$optionKey}' => '{$optionValue}',";
            }
            if (!empty($options)) {
                $options = rtrim($options, ',');
                $options = "[{$options}]";
            }
            $asString = str_replace(
                '{{ assertionOptions }}',
                $options,
                $asString
            );
        }

        return Template::create($asString);
    }
}
