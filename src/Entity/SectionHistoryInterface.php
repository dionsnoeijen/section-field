<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

interface SectionHistoryInterface
{
    public function setSection(SectionInterface $section): SectionHistoryInterface;
    public function getSection(): SectionInterface;
    public function removeSection(SectionInterface $section): SectionHistoryInterface;
}
