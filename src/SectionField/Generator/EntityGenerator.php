<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\TextInput\TextInputFieldType;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\EntityGenerator as EntityGeneratorInterface;
use Tardigrades\SectionField\ValueObject\EntityTemplate;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class EntityGenerator implements EntityGeneratorInterface
{
    /** @var FieldManager $fieldManager */
    private $fieldManager;

    public function __construct(FieldManager $fieldManager)
    {
        $this->fieldManager = $fieldManager;
    }

    public function generateBySection(Section $section): void
    {
        $sectionConfig = $section->getConfig();

        $this->generateSectionBase($sectionConfig);
    }

    private function generateMethods(array $fields): string
    {
        $fields = $this->fieldManager->readFieldsByHandles($fields);

        $methods = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            if ((string) $field->getFieldType()->getType() === 'TextInput') {
                $fieldTypeFullyQualifiedClassName = (string) $field
                    ->getFieldType()
                    ->getFullyQualifiedClassName();
                /** @var TextInputFieldType $fieldType */
                $fieldType = new $fieldTypeFullyQualifiedClassName();
                $fieldType->setConfig($field->getConfig());

                $methods .= (string) $fieldType->renderEntityMethods();
            }
        }

        return $methods;
    }

    private function generateSectionBase(SectionConfig $sectionConfig): string
    {
        $template = EntityTemplate::create(
            TemplateLoader::load(__DIR__ . '/GeneratorTemplate/entity.php.template')
        );

        $asString = (string) $template;
        $asString = str_replace('{{ section }}', $sectionConfig->getClassName(), $asString);
        $asString = str_replace(
            '{{ methods }}',
            $this->generateMethods($sectionConfig->getFields()),
            $asString
        );

        print_r($asString);

        return $asString;
    }
}
