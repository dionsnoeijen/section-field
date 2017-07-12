<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\SectionFieldInterface\LanguageManager;
use Tardigrades\Entity\EntityInterface\Language as LanguageInterface;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\SectionField\ValueObject\LanguageConfig;

class DoctrineLanguageManager implements LanguageManager
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

    public function create(LanguageInterface $entity): LanguageInterface
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): LanguageInterface
    {
        $languageRepository = $this->entityManager->getRepository(Language::class);

        /** @var $language Language */
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

    public function update(): void
    {
        $this->entityManager->flush();
    }

    public function delete(LanguageInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function readByI18n(I18n $i18n): LanguageInterface
    {
        $languageRepository = $this->entityManager->getRepository(Language::class);

        /** @var Language $language */
        $language = $languageRepository->findOneBy([
            'i18n' => (string) $i18n
        ]);

        if (empty($language)) {
            throw new LanguageNotFoundException();
        }

        return $language;
    }

    public function readByI18ns(array $i18ns): array
    {
        $in = [];
        foreach ($i18ns as $i18n) {
            $in[] = '\'' . $i18n . '\'';
        }
        $whereIn = implode(',', $in);
        $query = $this->entityManager->createQuery(
            "SELECT language FROM Tardigrades\Entity\Language language WHERE language.i18n IN ({$whereIn})"
        );
        $results = $query->getResult();

        if (empty($results)) {
            throw new LanguageNotFoundException();
        }

        $finalResults = [];
        /** @var Language $result */
        foreach ($results as $result) {
            $finalResults[(string) $result->getI18n()] = $result;
        }

        return $finalResults;
    }

    public function createByConfig(LanguageConfig $languageConfig): LanguageManager
    {
        $this->setUpByConfig($languageConfig);
        $this->entityManager->flush();

        return $this;
    }

    public function updateByConfig(LanguageConfig $languageConfig): LanguageManager
    {
        $this->setUpByConfig($languageConfig);
        $this->entityManager->flush();

        return $this;
    }

    public function setUpByConfig(LanguageConfig $languageConfig): void
    {
        $languageConfig = $languageConfig->toArray();

        try {
            $existing = $this->readByI18ns($languageConfig['language']);
        } catch (LanguageNotFoundException $exception) {
            $existing = [];
        }
        $existingCheck = [];
        /** @var Language $language */
        foreach ($existing as $language) {
            $existingCheck[] = (string) $language->getI18n();
        }

        foreach ($languageConfig['language'] as $configLanguage) {
            if (!in_array($configLanguage, $existingCheck)) {
                $this->entityManager->persist(
                    (new Language())->setI18n($configLanguage)
                );
            }
        }

    }
}
