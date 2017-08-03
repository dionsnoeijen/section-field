<?php

namespace Tardigrades\FieldType\Slug;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\Slug\SlugInterface\SlugFieldType;


class Slug extends FieldType implements SlugFieldType
{
    public function addToForm(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        $formBuilder->add((string) $this->getConfig()->getHandle(), TextType::class);

        return $formBuilder;
    }
}
