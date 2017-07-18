<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\EntityGenerator as EntityGeneratorInterface;
use Tardigrades\SectionField\ValueObject\EntityTemplate;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\FieldType\FieldTypeInterface\EntityGenerator as FieldTypeEntityGenerator;

class EntityGenerator implements EntityGeneratorInterface
{
    /** @var FieldManager */
    private $fieldManager;

    /** @var FieldTypeEntityGenerator */
    private $entityGenerator;

    public function __construct(
        FieldManager $fieldManager,
        FieldTypeEntityGenerator $entityGenerator
    ) {
        $this->fieldManager = $fieldManager;
        $this->entityGenerator = $entityGenerator;
    }

    public function generateBySection(Section $section): void
    {
        $sectionConfig = $section->getConfig();

        $fields = $this->fieldManager->readFieldsByHandles($sectionConfig->getFields());
        $this->generateSectionBase($sectionConfig, $fields);
    }

    private function generateProperties(array $fields): string
    {
        $properties = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $properties .= (string)$this->entityGenerator
                    ->renderEntityProperties(
                        $field->getConfig(),
                        $field
                            ->getFieldType()
                            ->getFullyQualifiedClassName()
                    );
            } catch (\Exception $exception) {
                unset($exception);
            }
        }

        return $properties;
    }

    private function generateMethods(array $fields): string
    {
        $methods = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $methods .= (string) $this->entityGenerator
                    ->renderEntityMethods(
                        $field->getConfig(),
                        $field
                            ->getFieldType()
                            ->getFullyQualifiedClassName()
                    );
            } catch (\Exception $exception) {
                unset($exception);
            }
        }

        return $methods;
    }

    private function generatePrePersist(array $fields): string
    {
        $prePersist = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $prePersist .= (string) $this->entityGenerator
                    ->renderPrePersist(
                        $field->getConfig(),
                        $field
                            ->getFieldType()
                            ->getFullyQualifiedClassName()
                    );
            } catch (\Exception $exception) {
                unset($exception);
            }
        }

        return $prePersist;
    }

    private function generatePreUpdate(array $fields): string
    {
        $preUpdate = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $preUpdate .= (string) $this->entityGenerator
                    ->renderPrePersist(
                        $field->getConfig(),
                        $field
                            ->getFieldType()
                            ->getFullyQualifiedClassName()
                    );
            } catch (\Exception $exception) {
                unset($exception);
            }
        }

        return $preUpdate;
    }

    private function generateSectionBase(SectionConfig $sectionConfig, array $fields): string
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

        print_r($asString);

        return $asString;
    }
}
