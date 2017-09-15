<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Tardigrades\FieldType\Slug\ValueObject\Slug;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\ValueObject\After;
use Tardigrades\SectionField\ValueObject\Before;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Limit;
use Tardigrades\SectionField\ValueObject\Offset;
use Tardigrades\SectionField\ValueObject\OrderBy;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\SlugField;

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
        $this->addSlugToQuery(
            $readOptions->getSlug(),
            $sectionConfig->getSlugField(),
            $readOptions->getSection()[0]
        );
        $this->addLimitToQuery($readOptions->getLimit());
        $this->addOffsetToQuery($readOptions->getOffset());
        $this->addOrderByToQuery($readOptions->getOrderBy(), $readOptions->getSection()[0]);
        $this->addBeforeToQuery($readOptions->getBefore(), $readOptions->getSection()[0]);
        $this->addAfterToQuery($readOptions->getAfter(), $readOptions->getSection()[0]);

        $query = $this->queryBuilder->getQuery();
        $results = $query->getResult();

        if (count($results) <= 0) {
            throw new EntryNotFoundException();
        }

        return new \ArrayIterator($results);
    }

    private function addSectionToQuery(FullyQualifiedClassName $section): void
    {
        $this->queryBuilder->select((string) $section->getClassName());
        $this->queryBuilder->from((string) $section, (string) $section->getClassName());
    }

    private function addIdToQuery(Id $id = null): void
    {
        if ($id instanceof Id) {
            $this->queryBuilder->where('id = :id');
            $this->queryBuilder->setParameter('id', $id->toInt());
        }
    }

    private function addSlugToQuery(
        Slug $slug = null,
        SlugField $slugField = null,
        FullyQualifiedClassName $section
    ): void {
        if ($slug instanceof Slug && $slugField instanceof SlugField) {
            $this->queryBuilder->where((string) $section->getClassName() . '.' . (string) $slugField . '= :slug');
            $this->queryBuilder->setParameter('slug', (string)$slug);
        }
    }

    private function addLimitToQuery(Limit $limit = null): void
    {
        if ($limit instanceof Limit) {
            $this->queryBuilder->setMaxResults($limit->toInt());
        }
    }

    private function addOffsetToQuery(Offset $offset = null): void
    {
        if ($offset instanceof Offset) {
            $this->queryBuilder->setFirstResult($offset->toInt());
        }
    }

    private function addOrderByToQuery(OrderBy $orderBy = null, FullyQualifiedClassName $section = null): void
    {
        if ($orderBy instanceof OrderBy && $section instanceof FullyQualifiedClassName) {
            $this->queryBuilder->orderBy(
                (string) $section->getClassName() . '.' . (string) $orderBy->getHandle(),
                (string) $orderBy->getSort()
            );
        }
    }

    /**
     * @todo: Created has the same problem as the slug field. Implement similar solution.
     *
     * @param Before $before
     * @param FullyQualifiedClassName $section
     */
    private function addBeforeToQuery(Before $before = null, FullyQualifiedClassName $section = null): void
    {
        if ($before instanceof Before && $section instanceof FullyQualifiedClassName) {
            $this->queryBuilder->where($section->getClassName() . '.created < :before');
            $this->queryBuilder->setParameter('before', (string) $before);
        }
    }

    private function addAfterToQuery(After $after = null, FullyQualifiedClassName $section = null): void
    {
        if ($after instanceof After && $section instanceof FullyQualifiedClassName) {
            $this->queryBuilder->where($section->getClassName() . '.created > :after');
            $this->queryBuilder->setParameter('after', (string) $after);
        }
    }
}
