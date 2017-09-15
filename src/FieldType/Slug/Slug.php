<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Slug;

use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class Slug extends FieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface {

//        $formBuilder->add((string) $this->getConfig()->getHandle(), TextType::class);

        return $formBuilder;
    }
}
