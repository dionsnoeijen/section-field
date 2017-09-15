<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

interface CreateSectionInterface
{
    public function save($data, array $jitRelationships = null);
}
