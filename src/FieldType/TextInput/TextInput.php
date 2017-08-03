<?php

namespace Tardigrades\FieldType\TextInput;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\FieldType\FieldType;

class TextInput extends FieldType implements TextInputFieldType
{
    public function addToForm(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        $formBuilder->add((string) $this->getConfig()->getHandle(), TextType::class);

        return $formBuilder;
    }
}
