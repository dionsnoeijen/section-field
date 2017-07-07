<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager as FieldTypeManagerInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;

class FieldTypeManager implements FieldTypeManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(FieldType $entity): FieldType
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): FieldType
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

    public function update(FieldType $entity): FieldType
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(FieldType $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function createWithFullyQualifiedClassName(FullyQualifiedClassName $fullyQualifiedClassName): FieldType
    {
        $fieldType = new FieldType();
        $fieldType->setType($fullyQualifiedClassName->getClassName());
        $fieldType->setFullyQualifiedClassName((string) $fullyQualifiedClassName);

        /** @var $fieldType FieldType */
        $fieldType = $this->create($fieldType);
        return $fieldType;
    }

    public function readByType(Type $type): FieldType
    {
        $fieldTypeRepo = $this->entityManager->getRepository(FieldType::class);

        /** @var $fieldType FieldType */
        $fieldType = $fieldTypeRepo->findOneBy([
            'type' => (string) $type
        ]);

        if (empty($fieldType)) {
            throw new FieldTypeNotFoundException();
        }

        return $fieldType;
    }
}
