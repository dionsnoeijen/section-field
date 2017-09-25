<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Tardigrades\SectionField\ValueObject\Versioned;

interface SectionHistoryInterface
{
    public function setSection(SectionInterface $section): SectionHistoryInterface;
    public function getSection(): SectionInterface;
    public function removeSection(SectionInterface $section): SectionHistoryInterface;
    public function setVersioned(\DateTime $versioned): SectionHistoryInterface;
    public function getVersioned(): Versioned;
}
