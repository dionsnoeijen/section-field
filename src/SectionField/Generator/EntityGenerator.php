<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\NoCustomPrePersistMethodDefinedException;
use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\FieldType\ValueObject\EntityPropertiesTemplate;
use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\FieldType\ValueObject\PreUpdateTemplate;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\Generator;
use Tardigrades\SectionField\ValueObject\EntityTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\SlugField;

class EntityGenerator implements Generator
{
    /** @var FieldManager */
    private $fieldManager;

    /** @var array */
    private $buildMessages = [];

    public function __construct(
        FieldManager $fieldManager
    ) {
        $this->fieldManager = $fieldManager;
    }

    public function generateBySection(Section $section): void
    {
        $sectionConfig = $section->getConfig();

        $fields = $this->fieldManager->readFieldsByHandles($sectionConfig->getFields());

        $this->generateSectionBase($sectionConfig, $fields);
    }

    public function getBuildMessages(): array
    {
        return $this->buildMessages;
    }

    protected function generateProperties(array $fields): string
    {
        $properties = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $properties .= (string) $this->renderEntityProperties(
                    $field->getConfig(),
                    $field
                        ->getFieldType()
                        ->getFullyQualifiedClassName()
                ). PHP_EOL;
            } catch (\Exception $exception) {
                $this->buildMessages[] = $field->getHandle() . ': ' . $exception->getMessage();
            }
        }

        return $properties;
    }

    protected function renderEntityProperties(
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

    protected function generateMethods(array $fields): string
    {
        $methods = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $methods .= (string) $this->renderEntityMethods(
                    $field->getConfig(),
                    $field
                        ->getFieldType()
                        ->getFullyQualifiedClassName()
                ) . PHP_EOL;
            } catch (\Exception $exception) {
                $this->buildMessages[] = $field->getHandle() . ': ' . $exception->getMessage();
            }
        }

        return $methods;
    }

    protected function renderEntityMethods(
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

        return EntityMethodsTemplate::create($asString);
    }

    protected function generatePrePersist(array $fields): string
    {
        $prePersist = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $fullyQualifiedClassName = (string) $field->getFieldType()->getFullyQualifiedClassName();

                if (!class_exists($fullyQualifiedClassName)) {
                    throw new FieldTypeDoesNotExistException();
                }

                $fieldInstance = new $fullyQualifiedClassName();
                $fieldInstance->setConfig($field->getConfig());

                $prePersist .= (string) $fieldInstance->renderPrePersist();

            } catch (\Exception $exception) {

                if ($exception instanceof FieldTypeDoesNotExistException ||
                    $exception instanceof NoCustomPrePersistMethodDefinedException
                ) {
                    try {
                        $prePersist .= (string) $this->renderPrePersist(
                            $field->getConfig(),
                            $field
                                ->getFieldType()
                                ->getFullyQualifiedClassName()
                        );
                    } catch (\Exception $exception) {
                        $this->buildMessages[] = $field->getHandle() . ': ' . $exception->getMessage();
                    }
                }
            }
        }

        return $prePersist;
    }

    protected function renderPrePersist(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): PrePersistTemplate
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

    protected function generatePreUpdate(array $fields): string
    {
        $preUpdate = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $preUpdate .= (string) $this->renderPreUpdate(
                    $field->getConfig(),
                    $field
                        ->getFieldType()
                        ->getFullyQualifiedClassName()
                );
            } catch (\Exception $exception) {
                $this->buildMessages[] = $field->getHandle() . ': ' . $exception->getMessage();
            }
        }

        return $preUpdate;
    }

    protected function renderPreUpdate(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): PreUpdateTemplate
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

    protected function generateSlugFieldGetMethod(SlugField $slugField)
    {
        return <<<EOT
public function getSlug(): Tardigrades\FieldType\Slug\ValueObject\Slug
{
    return \$this->{$slugField};
}
EOT;
    }

    protected function generateSectionBase(SectionConfig $sectionConfig, array $fields): string
    {
        $template = EntityTemplate::create(
            TemplateLoader::load(__DIR__ . '/GeneratorTemplate/entity.php.template')
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ properties }}',
            $this->generateProperties($fields),
            $asString
        );
        $asString = str_replace(
            '{{ methods }}',
            $this->generateMethods($fields),
            $asString
        );
        $asString = str_replace(
            '{{ preUpdate }}',
            $this->generatePreUpdate($fields),
            $asString
        );

        try {
            $asString = str_replace(
                '{{ getSlug }}',
                $this->generateSlugFieldGetMethod($sectionConfig->getSlugField()),
                $asString
            );
        } catch (\Exception $exception) {
            $asString = str_replace(
                '{{ getSlug }}',
                '',
                $asString
            );
            $this->buildMessages[] = 'There is no slug field available';
        }

        $asString = str_replace(
            '{{ prePersist }}',
            $this->generatePrePersist($fields),
            $asString
        );
        $asString = str_replace(
            '{{ section }}',
            $sectionConfig->getClassName(),
            $asString
        );

        $asString = PhpFormatter::format($asString);

        print_r($asString);

        return $asString;
    }

    protected function fullyQualifiedNameToDir(
        FullyQualifiedClassName $fullyQualifiedClassName
    ): string {
        $segments = explode('\\',
            explode(
                '/src',
                __DIR__
            )[0] . '/src/' .
            str_replace(
                'Tardigrades\\', // @todo: The vendor name must be configurable, not hardcoded.
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
}
