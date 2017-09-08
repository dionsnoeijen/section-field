<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\SectionFieldInterface\DeleteSection as DeleteSectionInterface;

class DeleteSection implements DeleteSectionInterface
{
    /** @var array */
    private $deleters;

    /**
     * DeleteSection constructor.
     * @param array $deleters
     */
    public function __construct(array $deleters)
    {
        $this->deleters = $deleters;
    }

    public function delete($sectionEntryEntity): bool
    {
        /** @var DeleteSectionInterface $deleter */
        foreach ($this->deleters as $deleter) {
            $deleter->delete($sectionEntryEntity);
        }
    }
}
