<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\FieldType\ValueObject\EntityPropertiesTemplate;
use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\FieldType\ValueObject\PreUpdateTemplate;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\FieldType\FieldTypeInterface\EntityGenerator as EntityGeneratorInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

class EntityGenerator implements EntityGeneratorInterface
{
    public function renderEntityMethods(
        FieldConfig $config,
        FullyQualifiedClassName $fullyQualifiedClassName
    ): EntityMethodsTemplate {

        $template = $this->getEntityMethodsTemplate(
            $this->fullyQualifiedNameToDir($fullyQualifiedClassName),
            $config
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ methodName }}',
            $config->getMethodName(),
            $asString
        );
        $asString = str_replace(
            '{{ propertyName }}',
            $config->getPropertyName(),
            $asString
        );

        return EntityMethodsTemplate::create(
            $asString
        );
    }

    public function renderEntityProperties(
        FieldConfig $config,
        FullyQualifiedClassName $fullyQualifiedClassName
    ): EntityPropertiesTemplate {

        $template = $this->getEntityPropertiesTemplate(
            $this->fullyQualifiedNameToDir($fullyQualifiedClassName)
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ propertyName }}',
            $config->getPropertyName(),
            $asString
        );

        return EntityPropertiesTemplate::create($asString);
    }

    public function renderPrePersist(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): PrePersistTemplate
    {
        $template = $this->getPrePersistTemplate(
            $this->fullyQualifiedNameToDir($fullyQualifiedClassName)
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ propertyName }}',
            $config->getPropertyName(),
            $asString
        );

        return PrePersistTemplate::create(
            $asString
        );
    }

    public function renderPreUpdate(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): PreUpdateTemplate
    {
        $template = $this->getPreUpdateTemplate(
            $this->fullyQualifiedNameToDir($fullyQualifiedClassName)
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ propertyName }}',
            $config->getPropertyName(),
            $asString
        );

        return PreUpdateTemplate::create(
            $asString
        );
    }

    protected function getEntityMethodsTemplate(string $dir, FieldConfig $config): EntityMethodsTemplate
    {
        return EntityMethodsTemplate::create(
            TemplateLoader::load($dir . '/GeneratorTemplate/entitymethods.php.template', $config)
        );
    }

    protected function getEntityPropertiesTemplate(string $dir): EntityPropertiesTemplate
    {
        return EntityPropertiesTemplate::create(
            TemplateLoader::load($dir . '/GeneratorTemplate/entityproperties.php.template')
        );
    }

    protected function getPrePersistTemplate(string $dir): PrePersistTemplate
    {
        return PrePersistTemplate::create(
            TemplateLoader::load($dir . '/GeneratorTemplate/prepersist.php.template')
        );
    }

    protected function getPreUpdateTemplate(string $dir): PreUpdateTemplate
    {
        return PreUpdateTemplate::create(
            TemplateLoader::load($dir . '/GeneratorTemplate/preupdate.php.template')
        );
    }

    private function fullyQualifiedNameToDir(
        FullyQualifiedClassName $fullyQualifiedClassName
    ): string {
        $segments = explode('\\',
            explode(
                '/src',
                __DIR__
            )[0] . '/src/' .
            str_replace(
                'Tardigrades\\',
                '',
                (string) $fullyQualifiedClassName
            )
        );

        array_pop($segments);

        $dir = str_replace(
            '\\',
            '/',
            implode('\\', $segments)
        );

        return $dir;
    }
}
