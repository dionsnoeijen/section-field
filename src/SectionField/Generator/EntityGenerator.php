<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\FieldType;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\TextInput\TextInputFieldType;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\EntityGenerator as EntityGeneratorInterface;

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
        $this->generateMethods($sectionConfig->getFields());
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

                print_r($fieldType->getConfig()->toArray());
            }
        }

        return $methods;
    }
}
