<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

interface DeleteSectionInterface
{
    public function delete($sectionEntryEntity): bool;
}
