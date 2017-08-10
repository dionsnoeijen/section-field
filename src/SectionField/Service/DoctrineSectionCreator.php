<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\SectionFieldInterface\CreateSection;
use Tardigrades\SectionField\ValueObject\JitRelationship;

class DoctrineSectionCreator implements CreateSection
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function save($data, array $jitRelationships = null)
    {
        try {
            $this->setReferencesForJitRelationships($data, $jitRelationships);
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    private function setReferencesForJitRelationships($data, array $jitRelationships): void
    {
        /** @var JitRelationship $jitRelationship */
        foreach ($jitRelationships as $jitRelationship) {
            $handle = FullyQualifiedClassNameConverter::toHandle(
                $jitRelationship->getFullyQualifiedClassName()
            );
            $reference = $this->entityManager->getReference(
                (string) $jitRelationship->getFullyQualifiedClassName(),
                $jitRelationship->getId()->toInt()
            );
            $singularMethod = 'set' . ucfirst($handle);
            if (method_exists($data, $singularMethod)) {
                $data->{$singularMethod}($reference);
            }
            $pluralMethod = 'add' . ucfirst($handle);
            if (method_exists($data, $pluralMethod)) {
                $data->{$pluralMethod}($reference);
            }
        }
    }
}
