<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Symfony\Component\Form\FormInterface;
use Tardigrades\Entity\EntityInterface\Section;

interface Form
{
    public function buildFormForSection(Section $section, $sectionEntity = null): FormInterface;
}
