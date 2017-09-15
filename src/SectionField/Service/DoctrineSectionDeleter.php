<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\ORM\EntityManagerInterface;

class DoctrineSectionDeleter implements DeleteSectionInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function delete($sectionEntryEntity): bool
    {
        $this->entityManager->remove($sectionEntryEntity);
    }
}
