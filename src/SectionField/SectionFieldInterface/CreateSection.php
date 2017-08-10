<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

interface CreateSection
{
    public function save($data, array $jitRelationships = null);
}
