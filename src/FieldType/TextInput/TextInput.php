<?php

namespace Tardigrades\FieldType\TextInput;

use Assert\Assertion;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Text;

class TextInput extends FieldType implements TextInputFieldType
{
    public function getEntityMethodsTemplate(): EntityMethodsTemplate
    {
        return EntityMethodsTemplate::create(
            TemplateLoader::load(__DIR__ . '/GeneratorTemplate/entitymethods.php.template')
        );
    }

    public function renderEntityMethods(): EntityMethodsTemplate
    {
        $template = $this->getEntityMethodsTemplate();

        /** @var FieldConfig $config */
        $config = $this->getConfig();

        $asString = (string) $template;

        $asString = str_replace('{{ methodName }}', $config->getMethodName(), $asString);
        $asString = str_replace('{{ propertyName }}', $config->getPropertyName(), $asString);

        return EntityMethodsTemplate::create($asString);
    }

    public function getFormType()
    {

    }
}
