<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\ValueObject\ReadOptions;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class DoctrineSectionReader implements ReadSection
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function read(ReadOptions $options, SectionConfig $sectionConfig = null): \ArrayIterator
    {
        // @todo: Implement the multi section thingie
        $section = $options->getSection();

        $findBy = [];
        $slug = $options->getSlug();
        if (!empty($slug)) {
            $findBy = [(string) $sectionConfig->getSlugField() => $slug];
        }

        $sectionRepository = $this->entityManager->getRepository((string) reset($section));

        /** @var \ArrayIterator $results */
        $results = $sectionRepository->findBy(
            $findBy,
            $options->getOrderBy(),
            $options->getLimit(),
            $options->getOffset()
        );

        if (count($results) <= 0) {
            throw new EntryNotFoundException();
        }

        return new \ArrayIterator($results);
    }
}
