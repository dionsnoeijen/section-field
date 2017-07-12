<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldTranslation;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;
use Tardigrades\SectionField\SectionFieldInterface\LanguageManager;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;

class DoctrineFieldManager implements FieldManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    /**
     * @var LanguageManager
     */
    private $languageManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        FieldTypeManager $fieldTypeManager,
        LanguageManager $languageManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldTypeManager = $fieldTypeManager;
        $this->languageManager = $languageManager;
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

    public function update(): void
    {
        $this->entityManager->flush();
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

    private function getTranslations(FieldConfig $fieldConfig, Field $field): array
    {
        $fieldConfig = $fieldConfig->toArray();

        $translations = [];
        foreach ($fieldConfig['field']['name'] as $name) {
            $translations[key($name)] = [
                'name' => array_shift($name)
            ];
        }
        foreach ($fieldConfig['field']['label'] as $label) {
            $lang = key($label);
            if (is_array($translations[$lang])) {
                $translations[$lang]['label'] = array_shift($label);
            } else {
                $translations[$lang] = [
                    'label' => array_shift($label)
                ];
            }
        }

        $languages = $this->languageManager->readByI18ns(array_keys($translations));
        $fieldTranslations = [];
        $existingFieldTranslations = $field->getFieldTranslations();
        /** @var FieldTranslation $existingFieldTranslation */
        foreach ($existingFieldTranslations as $existingFieldTranslation) {
            $existing[(string) $existingFieldTranslation->getLanguage()->getI18n()] = $existingFieldTranslation;
        }

        foreach ($translations as $lang=>$translation) {
            if (isset($languages[$lang])) {
                if (isset($existing[$lang])) {
                    $fieldTranslation = $existing[$lang];
                } else {
                    $fieldTranslation = new FieldTranslation();
                }
                $fieldTranslation->setName($translation['name']);
                $fieldTranslation->setLabel($translation['label']);
                $fieldTranslation->setLanguage($languages[$lang]);
                $fieldTranslations[] = $fieldTranslation;
            }
        }

        return $fieldTranslations;
    }

    private function setUpFieldByConfig(FieldConfig $fieldConfig, Field $field): Field
    {
        $translations = $this->getTranslations($fieldConfig, $field);

        $fieldConfig = $fieldConfig->toArray();
        $fieldType = $this->fieldTypeManager->readByType(Type::create($fieldConfig['field']['type']));

        foreach ($translations as $translation) {
            $field->removeFieldTranslation($translation);
            $field->addFieldTranslation($translation);
        }

        $field->setHandle($fieldConfig['field']['handle']);
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
        $results = $query->getResult();
        if (empty($results)) {
            throw new FieldNotFoundException();
        }

        return $results;
    }
}
