<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\RichTextArea;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\RichTextArea\RichTextAreaInterface\RichTextArea as RichTextAreaInterface;

class RichTextArea extends FieldType implements RichTextAreaInterface
{
    public function addToForm(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        $formBuilder->add((string) $this->getConfig()->getHandle(), TextareaType::class);

        return $formBuilder;
    }
}
