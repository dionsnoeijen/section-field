<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Symfony\Component\Form\FormInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\SectionFormOptions;

interface Form
{
    public function buildFormForSection(
        FullyQualifiedClassName $forHandle,
        SectionFormOptions $sectionFormOptions
    ): FormInterface;
}
