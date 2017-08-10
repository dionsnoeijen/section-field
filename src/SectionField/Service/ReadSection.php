<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection as ReadSectionInterface;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\ReadOptions;
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

    public function read(
        ReadOptions $options,
        SectionConfig $sectionConfig = null): \ArrayIterator
    {
        $sectionData = new \ArrayIterator();

        // The story goes as follows:
        // When we have a slug, the slug field is in the section
        // config, we need to make an extra query to get the rules
        // for querying based on slug.
        $slug = $options->getSlug();
        $section = null;
        if (!empty($slug)) {
            // @todo: We probably want to get rid of the section handle
            // and use the FullyQualifiedClassName of the entity instead
            // That way we don't have to go for this kind of weird magic
            // with the converter
            $section = $this->sectionManager->readByHandle(
                FullyQualifiedClassNameConverter::toHandle(
                    $options->getSection()[0]
                )
            )->getConfig();
        }

        /** @var ReadSectionInterface $reader */
        foreach ($this->readers as $reader) {
            // @todo: Don't just append... merge!!
            foreach ($reader->read($options, $section) as $entry) {
                $sectionData->append($entry);
            }
        }

        return $sectionData;
    }
}
