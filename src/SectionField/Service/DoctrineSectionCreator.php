<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\SectionField\SectionFieldInterface\CreateSection;

class DoctrineSectionCreator implements CreateSection
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function save($data)
    {
        try {
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}
