<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Tardigrades\FieldType\Slug\ValueObject\Slug;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Limit;
use Tardigrades\SectionField\ValueObject\Offset;
use Tardigrades\SectionField\ValueObject\ReadOptions;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class DoctrineSectionReader implements ReadSection
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var QueryBuilder */
    private $queryBuilder;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function read(ReadOptions $readOptions, SectionConfig $sectionConfig = null): \ArrayIterator
    {
        $this->queryBuilder = $this->entityManager->createQueryBuilder();

        $this->addSectionToQuery($readOptions->getSection()[0]);
        $this->addIdToQuery($readOptions->getId());
        $this->addSlugToQuery($readOptions->getSlug());
        $this->addSectionIdToQuery($readOptions->getSectionId());
        $this->addLimitToQuery($readOptions->getLimit());
        $this->addOffsetToQuery($readOptions->getOffset());
        $this->addOrderByToQuery($readOptions->getOrderBy());
        $this->addBeforeToQuery($readOptions->getBefore());
        $this->addAfterToQuery($readOptions->getAfter());



        $results = [];

//        $findBy = [];
//        $slug = $readOptions->getSlug();
//        if (!empty($slug)) {
//            $findBy = [(string) $sectionConfig->getSlugField() => $slug];
//        }
//
//        $id = $readOptions->getId();
//        if (!empty($id)) {
//            $findBy = ['id' => $readOptions->getId()->toInt()];
//        }
//
//        $sectionRepository = $this->entityManager
//            ->getRepository((string) $sectionConfig->getFullyQualifiedClassName());
//
//        /** @var \ArrayIterator $results */
//        $results = $sectionRepository->findBy(
//            $findBy,
//            $readOptions->getOrderBy(),
//            $readOptions->getLimit(),
//            $readOptions->getOffset()
//        );

        if (count($results) <= 0) {
            throw new EntryNotFoundException();
        }

        return new \ArrayIterator($results);
    }

    private function addSectionToQuery(FullyQualifiedClassName $section): void
    {

    }

    private function addIdToQuery(Id $id): void
    {

    }

    private function addSlugToQuery(Slug $slug): void
    {

    }

    private function addSectionIdToQuery(Id $id): void
    {

    }

    private function addLimitToQuery(Limit $limit): void
    {

    }

    private function addOffsetToQuery(Offset $offset): void
    {

    }

    private function addOrderByToQuery(array $orderBy): void
    {

    }

    private function addBeforeToQuery(): string
    {

    }

    private function addAfterToQuery(): string
    {

    }

    private function addLocaleEnabledToQuery(): string
    {

    }

    private function addLocaleToQuery(): string
    {

    }

    private function addSearchToQuery(): string
    {

    }

    private function addFieldToQuery(): string
    {

    }
}
