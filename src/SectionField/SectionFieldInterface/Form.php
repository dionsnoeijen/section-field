<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Symfony\Component\Form\FormInterface;
use Tardigrades\FieldType\Slug\ValueObject\Slug;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

interface Form
{
    public function buildFormForSection(
        FullyQualifiedClassName $forHandle,
        Slug $slug = null
    ): FormInterface;
}
