<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\SectionFieldInterface\LanguageManager as LanguageManagerInterface;
use Tardigrades\Entity\EntityInterface\Language;
use Tardigrades\Entity\Language as LanguageEntity;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Doctrine\ORM\EntityManagerInterface;

class LanguageManager implements LanguageManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function create(Language $entity): Language
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): Language
    {
        $languageRepository = $this->entityManager->getRepository(LanguageEntity::class);

        /** @var $language Language */
        $language = $languageRepository->find($id->toInt());

        if (empty($language)) {
            throw new LanguageNotFoundException();
        }

        return $language;
    }

    public function readAll(): array
    {
        $languageRepository = $this->entityManager->getRepository(LanguageEntity::class);
        $languages = $languageRepository->findAll();

        if (empty($languages)) {
            throw new LanguageNotFoundException();
        }

        return $languages;
    }

    public function update(): void
    {
        $this->entityManager->flush();
    }

    public function delete(Language $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function readByI18n(I18n $i18n): Language
    {
        $languageRepository = $this->entityManager->getRepository(LanguageEntity::class);

        /** @var Language $language */
        $language = $languageRepository->findOneBy([
            'i18n' => (string) $i18n
        ]);

        if (empty($language)) {
            throw new LanguageNotFoundException();
        }

        return $language;
    }
}
