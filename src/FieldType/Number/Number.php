<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Integer;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class Number extends FieldType
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
            NumberType::class,
            $options
        );

        return $formBuilder;
    }
}
