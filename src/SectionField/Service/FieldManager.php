<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManager;
use Tardigrades\Entity\Field;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager as FieldManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Id;

class FieldManager implements FieldManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    public function __construct(
        EntityManager $entityManager,
        FieldTypeManager $fieldTypeManager
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
        $field = $fieldRepository->find($id);

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
        $field = $this->updateByConfig($fieldConfig, new Field());

        return $field;
    }

    public function updateByConfig(FieldConfig $fieldConfig, Field $field): Field
    {
        $fieldConfig = $fieldConfig->toArray();
        $fieldType = $this->fieldTypeManager->readByType($fieldConfig['field']['type']);

        $field->setName($fieldConfig['field']['name']);
        $field->setHandle(StringConverter::toCamelCase($fieldConfig['field']['name']));
        $field->setFieldType($fieldType);
        $field->setConfig((object) $fieldConfig);

        $this->entityManager->persist($field);
        $this->entityManager->flush();

        return $field;
    }

    public function readFieldsByArray(array $fields): array
    {
        $fieldsConfig = [];
        foreach ($fields as $fieldConfig) {
            $fieldsConfig[] = '\'' . $fieldConfig . '\'';
        }
        $whereIn = implode(',', $fieldsConfig);
        $query = $this->entityManager->createQuery(
            "SELECT field FROM Tardigrades\Entity\Field field WHERE field.handle IN ({$whereIn})"
        );
        return $query->getResult();
    }
}
