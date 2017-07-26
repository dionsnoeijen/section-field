<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\Entity\EntityInterface\FieldType as FieldTypeInterface;

class DoctrineFieldTypeManager implements FieldTypeManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(FieldTypeInterface $entity): FieldTypeInterface
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): FieldTypeInterface
    {
        $fieldTypeRepo = $this->entityManager->getRepository(FieldType::class);
        /** @var $fieldType FieldType */
        $fieldType = $fieldTypeRepo->find($id->toInt());
        if (empty($fieldType)) {
            throw new FieldTypeNotFoundException();
        }
        return $fieldType;
    }

    public function readAll(): array
    {
        $fieldTypeRepository = $this->entityManager->getRepository(FieldType::class);
        $fieldTypes = $fieldTypeRepository->findAll();
        if (empty($fieldTypes)) {
            throw new FieldTypeNotFoundException();
        }
        return $fieldTypes;
    }

    public function update(): void
    {
        $this->entityManager->flush();
    }

    public function delete(FieldTypeInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function createWithFullyQualifiedClassName(FullyQualifiedClassName $fullyQualifiedClassName): FieldTypeInterface
    {
        $fieldType = new FieldType();
        $fieldType->setType($fullyQualifiedClassName->getClassName());
        $fieldType->setFullyQualifiedClassName((string) $fullyQualifiedClassName);

        /** @var $fieldType FieldType */
        $fieldType = $this->create($fieldType);
        return $fieldType;
    }

    public function readByType(Type $type): FieldTypeInterface
    {
        $fieldTypeRepository = $this->entityManager->getRepository(FieldType::class);
        /** @var $fieldType FieldType */
        $fieldType = $fieldTypeRepository->findOneBy([
            'type' => (string) $type
        ]);
        if (empty($fieldType)) {
            throw new FieldTypeNotFoundException();
        }
        return $fieldType;
    }

    public function readByFullyQualifiedClassName(FullyQualifiedClassName $fullyQualifiedClassName): FieldTypeInterface
    {
        $fieldTypeRepository = $this->entityManager->getRepository(FieldType::class);
        /** @var $fieldType FieldType */
        $fieldType = $fieldTypeRepository->findOneBy([
            'fullyQualifiedClassName' => (string) $fullyQualifiedClassName
        ]);
        if (empty($fieldType)) {
            throw new FieldTypeNotFoundException();
        }
        return $fieldType;
    }
}
