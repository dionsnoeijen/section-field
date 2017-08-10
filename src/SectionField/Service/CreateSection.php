<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\SectionFieldInterface\CreateSection as CreateSectionInterface;

class CreateSection implements CreateSectionInterface
{
    /** @var array */
    private $creators;

    public function __construct(array $creators)
    {
        $this->creators = $creators;
    }

    public function save($data, array $jitRelationships = null)
    {
        /** @var CreateSectionInterface $writer */
        foreach ($this->creators as $writer) {
            $writer->save($data, $jitRelationships);
        }
    }
}
