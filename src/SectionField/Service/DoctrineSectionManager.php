<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\Entity\Section as SectionEntity;
use Tardigrades\Entity\SectionHistory;
use Tardigrades\Entity\SectionHistoryInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Version;

class DoctrineSectionManager implements SectionManagerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DoctrineFieldManager */
    private $fieldManager;

    /** @var SectionHistoryManagerInterface */
    private $sectionHistoryManager;

    /** @var array */
    private $opposingRelationships = [
        'many-to-one' => 'one-to-many',
        'one-to-many' => 'many-to-one',
        'many-to-many' => 'many-to-many'
    ];

    /** @var array */
    private $opposingRealtionshipTypes = [
        'bidirectional' => 'unidirectional',
        'unidirectional' => 'bidirectional'
    ];

    public function __construct(
        EntityManagerInterface $entityManager,
        FieldManagerInterface $fieldManager,
        SectionHistoryManagerInterface $sectionHistoryManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldManager = $fieldManager;
        $this->sectionHistoryManager = $sectionHistoryManager;
    }

    public function create(SectionInterface $entity): SectionInterface
    {
        $entity->setVersion(1);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): SectionInterface
    {
        $sectionRepository = $this->entityManager->getRepository(SectionEntity::class);

        /** @var $section SectionInterface */
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

    public function update(SectionInterface $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete(SectionInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function createByConfig(SectionConfig $sectionConfig): SectionInterface
    {
        $section = new SectionEntity();
        $section->setVersion(0);
        $this->updateByConfig($sectionConfig, $section);

        return $section;
    }

    /**
     * Restore an old version of a section by it's handle and version
     *
     * 1. Fetch the currently active section
     * 2. Copy the section history data to the active section entity, including the version
     * 3. Move the currently active section to history
     * 4. Clean the field associations from the updated active section
     * 5. Fetch the fields from the 'old' section config
     * 6. Assign the fields to the section
     * 7. Persist the active section
     *
     * This section might be generated from an updated config yml. Meaning the current config might
     * not comply with the active section configuration anymore.
     *
     * This will only update the config stored in the database. The generators will have to be called
     * to make the version change complete.
     *
     * @param $sectionFromHistory
     * @return SectionInterface
     */
    public function restoreFromHistory(SectionInterface $sectionFromHistory): SectionInterface
    {
        /** @var SectionInterface $activeSection */
        $activeSection = $this->readByHandle($sectionFromHistory->getHandle()); // 1

        /** @var SectionInterface $newSectionHistory */
        $newSectionHistory = $this->copySectionDataToSectionHistoryEntity($activeSection); // 2
        $newSectionHistory->setVersioned(new \DateTime()); // 3
        $version = $this->getHighestVersion($newSectionHistory->getHandle());
        $newSectionHistory->setVersion(1 + $version->toInt());
        $this->sectionHistoryManager->create($newSectionHistory); // 3

        $updatedActiveSection = $this->copySectionHistoryDataToSectionEntity($sectionFromHistory, $activeSection); // 4

        $updatedActiveSection->removeFields(); // 5

        $fields = $this->fieldManager->readByHandles($updatedActiveSection->getConfig()->getFields()); // 6

        foreach ($fields as $field) {
            $updatedActiveSection->addField($field);
        } // 7

        $this->entityManager->persist($updatedActiveSection);
        $this->entityManager->flush();

        return $updatedActiveSection;
    }

    /**
     * A section config is stored in the section history before it's updated.
     *
     * 1. Copy the data from the Section entity to the SectionHistory entity
     * 2. Persist it in the entity history
     * 3. Bump the version (+1)
     * 4. Clear the field many to many relationships
     * 5. Fetch the fields based on the config
     * 6. Add them to the Section entity
     * 7. Set the fields with the config values
     * 8. Set the config
     * 9. Persist the entity
     *
     * This new section might be created from an updated config yml. It's recommended to copy the old
     * config yml before you update it. That way the config yml's history will be in compliance with the
     * config stored in the database.
     *
     * This will only update the config stored in the database. The generators will have to be called
     * to make the version change complete.
     *
     * @param SectionConfig $sectionConfig
     * @param SectionInterface $section
     * @return SectionInterface
     */
    public function updateByConfig(SectionConfig $sectionConfig, SectionInterface $section): SectionInterface
    {
        /** @var SectionInterface $sectionHistory */
        $sectionHistory = $this->copySectionDataToSectionHistoryEntity($section); // 1
        $sectionHistory->setVersioned(new \DateTime()); // 2
        $this->sectionHistoryManager->create($sectionHistory); // 2

        $version = $this->getHighestVersion($section->getHandle());

        $section->setVersion(1 + $version->toInt()); // 3

        $section->removeFields(); // 4

        $fields = $this->fieldManager->readByHandles($sectionConfig->getFields()); // 5

        foreach ($fields as $field) {
            $section->addField($field);
        } // 6

        $section->setName((string) $sectionConfig->getName()); // 7
        $section->setHandle((string) $sectionConfig->getHandle()); // 7
        $section->setConfig($sectionConfig->toArray()); // 8

        $this->entityManager->persist($section); // 9
        $this->entityManager->flush(); // 9

        return $section;
    }

    public function getHighestVersion(Handle $handle): Version
    {
        $version = Version::fromInt(1);
        $handle = (string) $handle;
        $query = $this->entityManager->createQuery(
            "SELECT MAX(section.version) FROM Tardigrades\Entity\SectionHistory section WHERE section.handle = '{$handle}'"
        );
        $results = $query->getResult();
        if (empty($results)) {
            return $version;
        }

        return Version::fromInt((int) $results[0][1]);
    }

    public function readByHandle(Handle $handle): SectionInterface
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
        /** @var SectionInterface $section */
        foreach ($sections as $section) {
            $fields = $this->fieldManager->readByHandles($section->getConfig()->getFields());

            $sectionHandle = (string) $section->getHandle();
            if (!isset($relationships[$sectionHandle])) {
                $relationships[$sectionHandle] = [];
            }

            /** @var FieldInterface $field */
            foreach ($fields as $field) {
                try {
                    $fieldHandle = (string) $field->getHandle();
                    $relationships[$sectionHandle][$fieldHandle] = [
                        'kind' => $field->getConfig()->getRelationshipKind(),
                        'to' => $field->getConfig()->getRelationshipTo(),
                        'fullyQualifiedClassName' => $field->getFieldType()->getFullyQualifiedClassName()
                    ];

                    $fieldConfig = $field->getConfig()->toArray();

                    // @todo: Add to value object
                    if (!empty($fieldConfig['field']['relationship-type'])) {
                        $relationships[$sectionHandle][$fieldHandle]['relationship-type'] =
                            $fieldConfig['field']['relationship-type'];
                    }

                    if (!empty($fieldConfig['field']['from'])) {
                        $relationships[$sectionHandle][$fieldHandle]['from'] = $fieldConfig['field']['from'];
                    }
                } catch (\Exception $exception) {}
            }
        }

        $relationships = $this->fillOpposingRelationshipSides($relationships);

        return $relationships;
    }

    private function copySectionDataToSectionHistoryEntity(SectionInterface $section): SectionHistoryInterface
    {
        $sectionHistory = new SectionHistory();

        $sectionHistory->setVersion(($section->getVersion()->toInt()));
        $sectionHistory->setConfig($section->getConfig()->toArray());
        $sectionHistory->setName((string) $section->getName());
        $sectionHistory->setHandle((string) $section->getHandle());
        $sectionHistory->setCreated($section->getCreated());
        $sectionHistory->setUpdated($section->getUpdated());
        $sectionHistory->setSection($section);

        return $sectionHistory;
    }

    private function copySectionHistoryDataToSectionEntity(
        SectionInterface $sectionHistory,
        SectionInterface $section
    ): SectionInterface {

        $section->setVersion($sectionHistory->getVersion()->toInt());
        $section->setConfig($sectionHistory->getConfig()->toArray());
        $section->setHandle((string) $sectionHistory->getHandle());
        $section->setName((string) $sectionHistory->getName());
        $section->setCreated($sectionHistory->getCreated());
        $section->setUpdated($sectionHistory->getUpdated());

        return $section;
    }

    private function fillOpposingRelationshipSides(array $relationships): array
    {
        foreach ($relationships as $sectionHandle=>$relationshipFields) {
            if (count($relationshipFields)) {
                foreach ($relationshipFields as $fieldHandle => $kindToFieldType) {
                    $relationships[$kindToFieldType['to']][$fieldHandle . '-opposite'] = [
                        'kind' => $this->opposingRelationships[$kindToFieldType['kind']],
                        'to' => $sectionHandle,
                        'fullyQualifiedClassName' => $kindToFieldType['fullyQualifiedClassName']
                    ];
                    if (!empty($kindToFieldType['from'])) {
                        $relationships[$kindToFieldType['to']][$fieldHandle . '-opposite']['from'] =
                            $kindToFieldType['to'];
                    }
                    if (!empty($kindToFieldType['relationship-type'])) {
                        $relationships[$kindToFieldType['to']][$fieldHandle . '-opposite']['relationship-type'] =
                            $this->opposingRealtionshipTypes[$kindToFieldType['relationship-type']];
                    }
                }
            }
        }

        return $relationships;
    }
}
