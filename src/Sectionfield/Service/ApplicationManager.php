<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager as ApplicationManagerInterface;
use Tardigrades\Entity\EntityInterface\Application;
use Tardigrades\Entity\Application as ApplicationEntity;
use Tardigrades\SectionField\ValueObject\Id;
use Doctrine\ORM\EntityManagerInterface;

class ApplicationManager implements ApplicationManagerInterface
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

    public function create(Application $entity): Application
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): Application
    {
        $applicationRepository = $this->entityManager->getRepository(ApplicationEntity::class);

        /** @var $application Application */
        $application = $applicationRepository->find($id->toInt());

        if (empty($application)) {
            throw new ApplicationNotFoundException();
        }

        return $application;
    }

    public function readAll(): array
    {
        $applicationRepository = $this->entityManager->getRepository(ApplicationEntity::class);
        $applications = $applicationRepository->findAll();

        if (empty($applications)) {
            throw new ApplicationNotFoundException();
        }

        return $applications;
    }

    public function update(): void
    {
        $this->entityManager->flush();
    }

    public function delete(Application $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
