<?php

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManager;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\Manager;
use Tardigrades\SectionField\SectionFieldInterface\StructureEntity;

class FieldTypeManager implements Manager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(StructureEntity $entity): StructureEntity
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function createWithNamespace(string $namespace): FieldType
    {
        $fieldType = new FieldType();
        $type = explode('\\', $namespace);
        $type = $type[count($type) - 1];
        $fieldType->setType($type);
        $fieldType->setNamespace($namespace);

        /** @var $fieldType FieldType */
        $fieldType = $this->create($fieldType);
        return $fieldType;
    }

    public function read(int $id): StructureEntity
    {
        $fieldTypeRepo = $this->entityManager->getRepository(FieldType::class);

        /** @var $fieldType FieldType */
        $fieldType = $fieldTypeRepo->find($id);

        if (empty($fieldType)) {
            throw new FieldTypeNotFoundException();
        }

        return $fieldType;
    }

    public function readByType(string $type): FieldType
    {
        $fieldTypeRepo = $this->entityManager->getRepository(FieldType::class);

        /** @var $fieldType FieldType */
        $fieldType = $fieldTypeRepo->findOneBy([
            'type' => $type
        ]);

        if (empty($fieldType)) {
            throw new FieldTypeNotFoundException();
        }

        return $fieldType;
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
}
