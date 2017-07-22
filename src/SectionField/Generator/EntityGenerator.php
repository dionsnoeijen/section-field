<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\NoCustomGeneratorDefinedException;
use Tardigrades\FieldType\NoCustomMethodsMethodDefinedException;
use Tardigrades\FieldType\NoCustomPrePersistMethodDefinedException;
use Tardigrades\FieldType\NoCustomPreUpdateMethodDefinedException;
use Tardigrades\FieldType\NoCustomPropertiesMethodDefinedException;
use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\FieldType\ValueObject\EntityPropertiesTemplate;
use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\FieldType\ValueObject\PreUpdateTemplate;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\CustomGeneratorLoader;
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
                $customGenerator = CustomGeneratorLoader::load($field);
                if (!method_exists($customGenerator, 'renderEntityProperties')) {
                    throw new NoCustomPropertiesMethodDefinedException();
                }
                $properties .= (string) $customGenerator->renderEntityProperties($field->getConfig());
            } catch (\Exception $exception) {
                if ($exception instanceof NoCustomGeneratorDefinedException ||
                    $exception instanceof NoCustomPropertiesMethodDefinedException
                ) {
                    try {
                        $properties .= (string)$this->renderEntityProperties(
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

        return $properties;
    }

    protected function generateMethods(array $fields): string
    {
        $methods = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $customGenerator = CustomGeneratorLoader::load($field);
                if (!method_exists($customGenerator, 'renderEntityMethods')) {
                    throw new NoCustomMethodsMethodDefinedException();
                }
                $methods .= (string) $customGenerator->renderEntityMethods($field->getConfig());
            } catch (\Exception $exception) {
                if ($exception instanceof NoCustomGeneratorDefinedException ||
                    $exception instanceof NoCustomMethodsMethodDefinedException
                ) {
                    try {
                        $methods .= (string)$this->renderEntityMethods(
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

        return $methods;
    }

    protected function generatePrePersist(array $fields): string
    {
        $prePersist = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $customGenerator = CustomGeneratorLoader::load($field);
                if (!method_exists($customGenerator, 'renderPrePersist')) {
                    throw new NoCustomPrePersistMethodDefinedException();
                }
                $prePersist .= (string) $customGenerator->renderPrePersist($field->getConfig());
            } catch (\Exception $exception) {
                if ($exception instanceof NoCustomGeneratorDefinedException ||
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

    protected function generatePreUpdate(array $fields): string
    {
        $preUpdate = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $customGenerator = CustomGeneratorLoader::load($field);
                if (!method_exists($customGenerator, 'renderPreUpdate')) {
                    throw new NoCustomPreUpdateMethodDefinedException();
                }
                $preUpdate .= (string) $customGenerator->renderPreUpdate($field->getConfig());
            } catch (\Exception $exception) {
                if ($exception instanceof NoCustomGeneratorDefinedException ||
                    $exception instanceof NoCustomPreUpdateMethodDefinedException
                ) {
                    try {
                        $preUpdate .= (string)$this->renderPreUpdate(
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

        return $preUpdate;
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

    private function renderEntityMethods(
        FieldConfig $config,
        FullyQualifiedClassName $fullyQualifiedClassName
    ): EntityMethodsTemplate {

        $asString = (string) EntityMethodsTemplate::create(
            TemplateLoader::load( FullyQualifiedClassNameConverter::toDir($fullyQualifiedClassName) . '/GeneratorTemplate/entitymethods.php.template', $config)
        );

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

    private function renderPrePersist(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): PrePersistTemplate
    {
        $asString = (string) PrePersistTemplate::create(
            TemplateLoader::load(FullyQualifiedClassNameConverter::toDir($fullyQualifiedClassName) . '/GeneratorTemplate/prepersist.php.template')
        );

        $asString = str_replace(
            '{{ propertyName }}',
            $config->getPropertyName(),
            $asString
        );

        return PrePersistTemplate::create(
            $asString
        );
    }

    private function renderPreUpdate(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): PreUpdateTemplate
    {
        $asString = (string) PreUpdateTemplate::create(
            TemplateLoader::load(FullyQualifiedClassNameConverter::toDir($fullyQualifiedClassName) . '/GeneratorTemplate/preupdate.php.template')
        );

        $asString = str_replace(
            '{{ propertyName }}',
            $config->getPropertyName(),
            $asString
        );

        return PreUpdateTemplate::create(
            $asString
        );
    }

    private function renderEntityProperties(
        FieldConfig $config,
        FullyQualifiedClassName $fullyQualifiedClassName
    ): EntityPropertiesTemplate {

        $asString = (string) EntityPropertiesTemplate::create(
            TemplateLoader::load(FullyQualifiedClassNameConverter::toDir($fullyQualifiedClassName) . '/GeneratorTemplate/entityproperties.php.template')
        );

        $asString = str_replace(
            '{{ propertyName }}',
            $config->getPropertyName(),
            $asString
        );

        return EntityPropertiesTemplate::create($asString);
    }
}
