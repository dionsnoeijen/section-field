<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Entity\Section as SectionEntity;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class DoctrineSectionManager implements SectionManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DoctrineFieldManager */
    private $fieldManager;

    /** @var array */
    private $opposingRelationships = [
        'many-to-one' => 'one-to-many',
        'one-to-many' => 'many-to-one'
    ];

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

    public function update(): void
    {
        $this->entityManager->flush();
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
        $fields = $this->fieldManager->readFieldsByHandles($sectionConfig->getFields());

        $section->setName((string) $sectionConfig->getName());
        $section->setHandle((string) $sectionConfig->getHandle());
        foreach ($fields as $field) {
            $section->addField($field);
        }
        $section->setConfig($sectionConfig->toArray());

        $this->entityManager->persist($section);
        $this->entityManager->flush();

        return $section;
    }

    public function readByHandle(string $handle): Section
    {
        $sectionRepository = $this->entityManager->getRepository(SectionEntity::class);

        /** @var SectionEntity $section */
        $section = $sectionRepository->findBy(['handle' => $handle]);

        if (empty($section)) {
            throw new SectionNotFoundException();
        }

        return $section[0];
    }

    public function readByHandles(array $handles): array
    {
        $sectionHandles = [];
        foreach ($handles as $handle) {
            $sectionHandles[] = '\'' . $handle . '\'';
        }
        $whereIn = implode(',', $sectionHandles);
        $query = $this->entityManager->createQuery(
            "SELECT section FROM Tardigrades\Entity\Section section WHERE section.handle IN ({$whereIn})"
        );
        $results = $query->getResult();
        if (empty($results)) {
            throw new SectionNotFoundException();
        }

        return $results;
    }

    public function getRelationshipsOfAll(): array
    {
        $relationships = [];
        $sections = $this->readAll();
        /** @var Section $section */
        foreach ($sections as $section) {
            $fields = $this->fieldManager->readFieldsByHandles($section->getConfig()->getFields());

            $sectionHandle = (string) $section->getHandle();
            if (!isset($relationships[$sectionHandle])) {
                $relationships[$sectionHandle] = [];
            }

            /** @var Field $field */
            foreach ($fields as $field) {
                try {
                    $fieldHandle = (string) $field->getHandle();
                    $relationships[$sectionHandle][$fieldHandle] = [
                        'kind' => $field->getConfig()->getRelationshipKind(),
                        'to' => $field->getConfig()->getRelationshipTo(),
                        'fullyQualifiedClassName' => $field->getFieldType()->getFullyQualifiedClassName()
                    ];
                } catch (\Exception $exception) {}
            }
        }

        $relationships = $this->fillOpposingRelationshipSides($relationships);

        return $relationships;
    }

    private function fillOpposingRelationshipSides(array $relationships): array
    {
        foreach ($relationships as $sectionHandle=>$relationshipFields) {
            if (count($relationshipFields)) {
                foreach ($relationshipFields as $fieldHandle=>$kindToFieldType) {
                    $relationships[$kindToFieldType['to']][$fieldHandle . '-opposite'] = [
                        'kind' => $this->opposingRelationships[$kindToFieldType['kind']],
                        'to' => $sectionHandle,
                        'fullyQualifiedClassName' => $kindToFieldType['fullyQualifiedClassName']
                    ];
                }
            }
        }

        return $relationships;
    }
}
