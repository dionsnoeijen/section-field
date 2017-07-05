<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\SectionFieldInterface\LanguageManager as LanguageManagerInterface;
use Tardigrades\Entity\EntityInterface\Language;
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
        $languageRepository = $this->entityManager->getRepository(Language::class);

        /** @var $field Language */
        $language = $languageRepository->find($id->toInt());

        if (empty($language)) {
            throw new LanguageNotFoundException();
        }

        return $language;
    }

    public function readAll(): array
    {
        $languageRepository = $this->entityManager->getRepository(Language::class);
        $languages = $languageRepository->findAll();

        if (empty($languages)) {
            throw new LanguageNotFoundException();
        }

        return $languages;
    }

    public function update(Language $entity): Language
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(Language $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
