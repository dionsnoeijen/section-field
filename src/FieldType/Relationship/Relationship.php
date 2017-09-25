<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship;

use Doctrine\Common\Util\Inflector;
use Mockery\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Service\ReadOptions;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\Handle;

class Relationship extends FieldType
{
    const MANY_TO_MANY = 'many-to-many';
    const ONE_TO_MANY = 'one-to-many';
    const MANY_TO_ONE = 'many-to-one';

    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection
    ): FormBuilderInterface {

        switch ($this->getConfig()->getKind()) {
            case self::MANY_TO_MANY:
                return $this->addManyToManyToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity,
                    $section
                );
                break;
            case self::ONE_TO_MANY:
                $this->addOneToManyToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity,
                    $section
                );
                break;
            case self::MANY_TO_ONE:
                $this->addManyToOneToForm(
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
        ReadSectionInterface $readSection,
        SectionManagerInterface $sectionManager,
        $sectionEntity,
        SectionInterface $section
    ): FormBuilderInterface {

        $fieldConfig = $this->getConfig()->toArray();

        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($fieldConfig['field']['to']));

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
                'mapped' => false
            ]
        );

        return $formBuilder;
    }

    private function addOneToManyToForm(
        FormBuilderInterface $formBuilder,
        ReadSectionInterface $readSection,
        SectionManagerInterface $sectionManager,
        $sectionEntity,
        SectionInterface $section
    ): FormBuilderInterface {

        $fieldConfig = $this->getConfig()->toArray();

        

        return $formBuilder;
    }

    private function addManyToOneToForm(
        FormBuilderInterface $formBuilder,
        ReadSectionInterface $readSection,
        SectionManagerInterface $sectionManager,
        $sectionEntity,
        SectionInterface $section
    ): FormBuilderInterface {

        $fieldConfig = $this->getConfig()->toArray();

        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($fieldConfig['field']['to']));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        $toHandle = $fieldConfig['field']['to'];
        $selectedEntity = $sectionEntity->{'get' . ucfirst($fieldConfig['field']['to'])}();

        try {
            $entries = $readSection->read(ReadOptions::fromArray([
                'section' => $fullyQualifiedClassName
            ]));
        } catch (Exception $exception) {
            $entries = [];
        }

        $choices = [ '' => '...' ];
        foreach ($entries as $entry) {
            $choices[$entry->getDefault()] = $entry;
        }

        $formBuilder->add(
            $toHandle,
            ChoiceType::class, [
                'choices' => $choices,
                'data' => $selectedEntity,
                'multiple' => false
            ]
        );

        return $formBuilder;
    }
}
