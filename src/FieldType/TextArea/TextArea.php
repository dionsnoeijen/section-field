<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\TextArea;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\TextArea\TextAreaInterface\TextArea as TextAreaInterface;

class TextArea extends FieldType implements TextAreaInterface
{
    public function addToForm(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        $formBuilder->add((string) $this->getConfig()->getHandle(), TextareaType::class);

        return $formBuilder;
    }
}
