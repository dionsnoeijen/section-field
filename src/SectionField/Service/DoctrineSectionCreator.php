<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\ValueObject\JitRelationship;

class DoctrineSectionCreator implements CreateSectionInterface
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
        $this->setReferencesForJitRelationships($data, $jitRelationships);
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * Jit relationships are introduced due to the inability of symfony forms
     * to update a relationship by it's ID. It requires you to use the EntityType
     * for forms. But the EntityType is a symfony specific field type.
     *
     * @param $data
     * @param array $jitRelationships
     */
    private function setReferencesForJitRelationships($data, array $jitRelationships): void
    {
        $handles = [];
        /** @var JitRelationship $jitRelationship */
        foreach ($jitRelationships as $jitRelationship) {
            $handle = FullyQualifiedClassNameConverter::toHandle(
                $jitRelationship->getFullyQualifiedClassName()
            );
            $methodPartHandle = ucfirst((string) $handle);
            if (!isset($handles[(string) $handle])) {
                $handles[(string) $handle] = false;
            }
            $reference = $this->entityManager->getReference(
                (string) $jitRelationship->getFullyQualifiedClassName(),
                $jitRelationship->getId()->toInt()
            );
            if (!$handles[(string) $handle]) {
                $removeMethod = 'remove' . $methodPartHandle;
                $pluralGetMethod = 'get' . ucfirst(Inflector::pluralize((string) $handle));
                if (method_exists($data, $removeMethod) &&
                    method_exists($data, $pluralGetMethod)) {
                    $existingRelationships = $data->{$pluralGetMethod}();
                    foreach ($existingRelationships as $existingRelationship) {
                        $data->{$removeMethod}($existingRelationship);
                    }
                }
                $handles[(string) $handle] = true;
            }
            $setMethod = 'set' . $methodPartHandle;
            if (method_exists($data, $setMethod)) {
                $data->{$setMethod}($reference);
            }
            $addMethod = 'add' . $methodPartHandle;
            if (method_exists($data, $addMethod)) {
                $data->{$addMethod}($reference);
            }
        }
    }
}
