<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\ValueObject\SectionFormOptions;

interface FormInterface
{
    public function buildFormForSection(
        string $forHandle,
        SectionFormOptions $sectionFormOptions = null,
        bool $csrfProtection = true
    ): FormInterface;

    public function hasRelationship(array $formData): array;
}
