<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\RichTextArea;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\RichTextArea\RichTextAreaInterface\RichTextArea as RichTextAreaInterface;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class RichTextArea extends FieldType implements RichTextAreaInterface
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface {

        $options = $this->formOptions($sectionEntity);
        $options['required'] = $this->isRequired($section);

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            TextareaType::class,
            $options
        );

        return $formBuilder;
    }
}
