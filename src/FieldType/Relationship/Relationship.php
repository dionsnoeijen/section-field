<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship;

use Doctrine\Common\Util\Inflector;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\Relationship\RelationshipInterface\Relationship as RelationshipInterface;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\ReadOptions;

class Relationship extends FieldType implements RelationshipInterface
{

    public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface {
        switch ($this->getConfig()->getKind()) {
            case 'many-to-many':
                return $this->addManyToManyToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity,
                    $section
                );
                break;
        }

        return $formBuilder;
    }

    private function addManyToManyToForm(
        FormBuilderInterface $formBuilder,
        ReadSection $readSection,
        SectionManager $sectionManager,
        $sectionEntity,
        Section $section
    ): FormBuilderInterface {

        $fieldConfig = $this->getConfig()->toArray();

        $sectionTo = $sectionManager
            ->readByHandle($fieldConfig['field']['to']);

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        $entries = $readSection->read(ReadOptions::fromArray([
            'section' => $fullyQualifiedClassName
        ]));

        $choices = [];
        foreach ($entries as $entry) {
            $choices[$entry->getDefault()] =
                $fullyQualifiedClassName . ':' . $entry->getId();
        }

        $toHandle = Inflector::pluralize($fieldConfig['field']['to']);
        $selectedEntities = $sectionEntity->{'get' . ucfirst($toHandle)}();

        $selected = [];
        foreach ($selectedEntities as $selectedEntity) {
            $selected[] = get_class($selectedEntity) . ':' . $selectedEntity->getId();
        }

        $formBuilder->add(
            $toHandle . '_id',
            ChoiceType::class, [
                'choices' => $choices,
                'data' => $selected,
                'multiple' => true,
                'mapped' => false,
                'required' => $this->isRequired($section)
            ]
        );

        return $formBuilder;
    }
}
