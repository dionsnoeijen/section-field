<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\Entity\Field as FieldEntity;
use Tardigrades\SectionField\Generator\Writer\Writable;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\SectionFieldInterface\Generator as GeneratorInterface;

abstract class Generator implements GeneratorInterface
{
    /** @var FieldManager */
    protected $fieldManager;

    /** @var FieldTypeManager */
    protected $fieldTypeManager;

    /** @var SectionManager */
    protected $sectionManager;

    /** @var array */
    protected $relationships;

    /** @var array */
    protected $buildMessages;

    public function __construct(
        FieldManager $fieldManager,
        FieldTypeManager $fieldTypeManager,
        SectionManager $sectionManager
    ) {
        $this->fieldManager = $fieldManager;
        $this->fieldTypeManager = $fieldTypeManager;
        $this->sectionManager = $sectionManager;

        $this->relationships = $this->sectionManager->getRelationshipsOfAll();
    }

    protected function addOpposingRelationships(Section $section, array $fields): array
    {
        foreach ($this->relationships[(string) $section->getHandle()] as $fieldHandle=>$relationship) {
            if (false !== strpos($fieldHandle, '-opposite')) {
                $oppositeRelationshipField = new FieldEntity();
                // @todo: I sense the field labels are going to be a problem.
                // I propbably need a config value for the default language and use it here
                // Also, the relationship opposite side might require more configuration
                // make that available in the field config and use it here
                $oppositeRelationshipField->setConfig([
                    'field' => [
                        'name' => $fieldHandle,
                        'handle' => $fieldHandle,
                        'label' => ['en_EN' => $fieldHandle],
                        'kind' => $relationship['kind'],
                        'to' => $relationship['to']
                    ]
                ]);
                $oppositeRelationshipFieldType = $this->fieldTypeManager
                    ->readByFullyQualifiedClassName(
                        $relationship['fullyQualifiedClassName']
                    );
                $oppositeRelationshipField->setFieldType($oppositeRelationshipFieldType);
                $fields[] = $oppositeRelationshipField;
            }
        }

        return $fields;
    }

    public function getBuildMessages(): array
    {
        return $this->buildMessages;
    }

    abstract public function generateBySection(Section $section): Writable;
}
