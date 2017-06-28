<?php

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManager;
use Tardigrades\Entity\Section;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\SectionFieldInterface\Manager;
use Tardigrades\SectionField\SectionFieldInterface\StructureEntity;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class SectionManager implements Manager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(
        EntityManager $entityManager,
        FieldManager $fieldManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldManager = $fieldManager;
    }

    public function create(StructureEntity $entity): StructureEntity
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(int $id): StructureEntity
    {
        $sectionRepository = $this->entityManager->getRepository(Section::class);

        /** @var $section Section */
        $section = $sectionRepository->find($id);

        if (empty($section)) {
            throw new SectionNotFoundException();
        }

        return $section;
    }

    public function update(StructureEntity $entity): StructureEntity
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(StructureEntity $entity): bool
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return true;
    }

    public function createFromConfig(SectionConfig $sectionConfig): Section
    {
        $section = new Section();
        $this->updateFromConfig($sectionConfig, $section);

        return $section;
    }

    public function updateFromConfig(SectionConfig $sectionConfig, Section $section): Section
    {
        $sectionConfig = $sectionConfig->toArray();

        $fields = $this->fieldManager->readFieldsByArray($sectionConfig['section']['fields']);

        $section->setName($sectionConfig['section']['name']);
        $section->setHandle(StringConverter::toCamelCase($sectionConfig['section']['name']));
        foreach ($fields as $field) {
            $section->addField($field);
        }
        $section->setConfig((object) $sectionConfig);

        $this->entityManager->persist($section);
        $this->entityManager->flush();

        return $section;
    }
}
