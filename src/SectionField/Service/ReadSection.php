<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\SectionFieldInterface\ReadSection as ReadSectionInterface;
use Tardigrades\SectionField\ValueObject\Handle;

class ReadSection implements ReadSectionInterface
{
    /** @var array */
    private $readers;

    public function __construct(array $readers)
    {
        $this->readers = $readers;
    }

    public function read(Handle $sectionHandle, array $options): \ArrayIterator
    {
        $sectionData = new \ArrayIterator();

        /** @var ReadSectionInterface $reader */
        foreach ($this->readers as $reader) {
            // @todo: Don't just append... merge!!
            $sectionData->append($reader->read($sectionHandle, $options));
        }

        return $sectionData;
    }
}
