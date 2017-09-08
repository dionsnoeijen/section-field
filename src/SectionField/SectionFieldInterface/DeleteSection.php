<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

interface DeleteSection
{
    public function delete($sectionEntryEntity): bool;
}
