<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\Field as FieldEntity;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Generator\Writer\Writable;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

abstract class Generator implements GeneratorInterface
{
    /** @var FieldManagerInterface */
    protected $fieldManager;

    /** @var FieldTypeManagerInterface */
    protected $fieldTypeManager;

    /** @var SectionManagerInterface */
    protected $sectionManager;

    /** @var array */
    protected $relationships;

    /** @var array */
    protected $buildMessages = [];

    public function __construct(
        FieldManagerInterface $fieldManager,
        FieldTypeManagerInterface $fieldTypeManager,
        SectionManagerInterface $sectionManager
    ) {
        $this->fieldManager = $fieldManager;
        $this->fieldTypeManager = $fieldTypeManager;
        $this->sectionManager = $sectionManager;

        $this->relationships = [];
    }

    protected function addOpposingRelationships(SectionInterface $section, array $fields): array
    {
        $this->relationships = $this->sectionManager->getRelationshipsOfAll();
        foreach ($this->relationships[(string) $section->getHandle()] as $fieldHandle=>$relationship) {
            if (false !== strpos($fieldHandle, '-opposite')) {

                $fieldHandle = str_replace('-opposite', '', $fieldHandle);

                $oppositeRelationshipField = new FieldEntity();
                // @todo: I sense the field labels are going to be a problem.
                // I propbably need a config value for the default language and use it here
                // Also, the relationship opposite side might require more configuration
                // make that available in the field config and use it here
                $config = [
                    'field' => [
                        'name' => $fieldHandle,
                        'handle' => $fieldHandle,
                        'label' => ['en_EN' => $fieldHandle],
                        'kind' => $relationship['kind'],
                        'to' => $relationship['to'],
                        'from' => $relationship['from']
                    ]
                ];

                if (!empty($relationship['from'])) {
                    $config['field']['from'] = $relationship['from'];
                }
                if (!empty($relationship['relationship-type'])) {
                    $config['field']['relationship-type'] = $relationship['relationship-type'];
                }
                $oppositeRelationshipField->setConfig($config);
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

    abstract public function generateBySection(SectionInterface $section): Writable;
}
