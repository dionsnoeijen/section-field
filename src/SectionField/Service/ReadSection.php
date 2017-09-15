<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection as ReadSectionInterface;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class ReadSection implements ReadSectionInterface
{
    /** @var array */
    private $readers;

    /** @var SectionManager */
    private $sectionManager;

    public function __construct(
        array $readers,
        SectionManager $sectionManager
    ) {
        $this->readers = $readers;
        $this->sectionManager = $sectionManager;
    }

    /**
     *
     *
     * @param ReadOptions $options
     * @param SectionConfig|null $sectionConfig
     * @return \ArrayIterator
     */
    public function read(
        ReadOptions $options,
        SectionConfig $sectionConfig = null
    ): \ArrayIterator {
        $sectionData = new \ArrayIterator();

        if ($sectionConfig === null) {
            $sectionConfig = $this->sectionManager->readByHandle(
                FullyQualifiedClassNameConverter::toHandle(
                    $options->getSection()[0]
                )
            )->getConfig();
        }
        
        $optionsArray = $options->toArray();
        $optionsArray[ReadOptions::SECTION] = (string) $sectionConfig->getFullyQualifiedClassName();
        $options = ReadOptions::fromArray($optionsArray);

        /** @var ReadSectionInterface $reader */
        foreach ($this->readers as $reader) {
            foreach ($reader->read($options, $sectionConfig) as $entry) {
                $sectionData->append($entry);
            }
        }

        return $sectionData;
    }
}
