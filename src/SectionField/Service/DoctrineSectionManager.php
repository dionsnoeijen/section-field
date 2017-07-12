<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Entity\Section as SectionEntity;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class DoctrineSectionManager implements SectionManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DoctrineFieldManager
     */
    private $fieldManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        FieldManager $fieldManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldManager = $fieldManager;
    }

    public function create(Section $entity): Section
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): Section
    {
        $sectionRepository = $this->entityManager->getRepository(SectionEntity::class);

        /** @var $section Section */
        $section = $sectionRepository->find($id->toInt());

        if (empty($section)) {
            throw new SectionNotFoundException();
        }

        return $section;
    }

    public function readAll(): array
    {
        $sectionRepository = $this->entityManager->getRepository(SectionEntity::class);
        $sections = $sectionRepository->findAll();

        if (empty($sections)) {
            throw new SectionNotFoundException();
        }

        return $sections;
    }

    public function update(Section $entity): Section
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(Section $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function createByConfig(SectionConfig $sectionConfig): Section
    {
        $section = new SectionEntity();
        $this->updateByConfig($sectionConfig, $section);

        return $section;
    }

    public function updateByConfig(SectionConfig $sectionConfig, Section $section): Section
    {
        $sectionConfig = $sectionConfig->toArray();

        $fields = $this->fieldManager->readFieldsByHandles($sectionConfig['section']['fields']);

        $section->setName($sectionConfig['section']['name']);
        $section->setHandle(StringConverter::toCamelCase($sectionConfig['section']['name']));
        foreach ($fields as $field) {
            $section->addField($field);
        }
        $section->setConfig($sectionConfig);

        $this->entityManager->persist($section);
        $this->entityManager->flush();

        return $section;
    }
}
