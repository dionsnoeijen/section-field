<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class ReadSection implements ReadSectionInterface
{
    /** @var array */
    private $readers;

    /** @var SectionManagerInterface */
    private $sectionManager;

    public function __construct(
        array $readers,
        SectionManagerInterface $sectionManager
    ) {
        $this->readers = $readers;
        $this->sectionManager = $sectionManager;
    }

    /**
     * Read from one or more data-sources
     *
     * @param ReadOptionsInterface $options
     * @param SectionConfig|null $sectionConfig
     * @return \ArrayIterator
     */
    public function read(
        ReadOptionsInterface $options,
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

        // Make sure we are passing the fully qualified class name as the section
        $optionsArray = $options->toArray();
        $optionsArray[ReadOptions::SECTION] = (string) $sectionConfig->getFullyQualifiedClassName();
        // For now, we call DoctrineRead options, this will of course be fixed in a later release.
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
