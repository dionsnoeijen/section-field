<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Integer;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\FieldTypeInterface;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class Integer extends FieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface {
        $options = $this->formOptions($sectionEntity);

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            IntegerType::class,
            $options
        );

        return $formBuilder;
    }
}
