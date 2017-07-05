<?php
declare (strict_types=1);

namespace Tardigrades\Entity\EntityInterface;

use Doctrine\Common\Collections\Collection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Updated;

interface Field
{
    public function setId(int $id): Field;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function setHandle(string $handle): Field;
    public function getHandle(): Handle;
    public function addSection(Section $section): Field;
    public function removeSection(Section $section): Field;
    public function getSections(): Collection;
    public function setFieldType(FieldType $fieldType): Field;
    public function removeFieldType(FieldType $fieldType): Field;
    public function getFieldType(): FieldType;
    public function setConfig(array $config): Field;
    public function getConfig(): FieldConfig;
    public function getFieldTranslations(): Collection;
    public function addFieldTranslation(FieldTranslation $fieldTranslation): Field;
    public function removeFieldTranslation(FieldTranslation $fieldTranslation): Field;
    public function setCreated(\DateTime $created): Field;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): Field;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
