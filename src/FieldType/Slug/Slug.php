<?php

namespace Tardigrades\FieldType\Slug;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\Slug\SlugInterface\SlugFieldType;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;


class Slug extends FieldType implements SlugFieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface {

//        $formBuilder->add((string) $this->getConfig()->getHandle(), TextType::class);

        return $formBuilder;
    }
}
