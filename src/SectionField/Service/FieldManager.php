<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Entity\Field;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager as FieldManagerInterface;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager as FieldTypeManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;

class FieldManager implements FieldManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        FieldTypeManagerInterface $fieldTypeManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldTypeManager = $fieldTypeManager;
    }

    public function create(Field $entity): Field
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): Field
    {
        $fieldRepository = $this->entityManager->getRepository(Field::class);

        /** @var $field Field */
        $field = $fieldRepository->find($id->toInt());

        if (empty($field)) {
            throw new FieldNotFoundException();
        }

        return $field;
    }

    public function readAll(): array
    {
        $fieldRepository = $this->entityManager->getRepository(Field::class);
        $fields = $fieldRepository->findAll();

        if (empty($fields)) {
            throw new FieldNotFoundException();
        }

        return $fields;
    }

    public function update(Field $entity): Field
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(Field $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function createByConfig(FieldConfig $fieldConfig): Field
    {
        $field = $this->setUpFieldByConfig($fieldConfig, new Field());

        $this->entityManager->persist($field);
        $this->entityManager->flush();

        return $field;
    }

    public function updateByConfig(FieldConfig $fieldConfig, Field $field): Field
    {
        $field = $this->setUpFieldByConfig($fieldConfig, $field);

        $this->entityManager->flush();

        return $field;
    }

    private function setUpFieldByConfig(FieldConfig $fieldConfig, Field $field): Field
    {
        $fieldConfig = $fieldConfig->toArray();
        $fieldType = $this->fieldTypeManager->readByType(Type::create($fieldConfig['field']['type']));

        $field->setName($fieldConfig['field']['name']);
        $field->setHandle(StringConverter::toCamelCase($fieldConfig['field']['name']));
        $field->setFieldType($fieldType);
        $field->setConfig($fieldConfig);

        return $field;
    }

    public function readFieldsByHandles(array $handles): array
    {
        $fieldHandles = [];
        foreach ($handles as $handle) {
            $fieldHandles[] = '\'' . $handle . '\'';
        }
        $whereIn = implode(',', $fieldHandles);
        $query = $this->entityManager->createQuery(
            "SELECT field FROM Tardigrades\Entity\Field field WHERE field.handle IN ({$whereIn})"
        );
        return $query->getResult();
    }
}
