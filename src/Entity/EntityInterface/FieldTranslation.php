<?php
declare (strict_types=1);

namespace Tardigrades\Entity\EntityInterface;

use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Label;
use Tardigrades\SectionField\ValueObject\Updated;

interface FieldTranslation {
    public function setId(int $id): FieldTranslation;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function getName(): Name;
    public function setName(string $name): FieldTranslation;
    public function getLabel(): Label;
    public function setLabel(string $label): FieldTranslation;
    public function getField(): Field;
    public function setField(Field $field): FieldTranslation;
    public function removeField(Field $field): FieldTranslation;
    public function setLanguage(Language $language): FieldTranslation;
    public function getLanguage(): Language;
    public function setCreated(\DateTime $created): FieldTranslation;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): FieldTranslation;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
