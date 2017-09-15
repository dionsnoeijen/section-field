<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Label;
use Tardigrades\SectionField\ValueObject\Updated;

interface FieldTranslationInterface
{
    public function setId(int $id): FieldTranslationInterface;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function getName(): Name;
    public function setName(string $name): FieldTranslationInterface;
    public function getLabel(): Label;
    public function setLabel(string $label): FieldTranslationInterface;
    public function getField(): FieldInterface;
    public function setField(FieldInterface $field): FieldTranslationInterface;
    public function removeField(FieldInterface $field): FieldTranslationInterface;
    public function setLanguage(LanguageInterface $language): FieldTranslationInterface;
    public function getLanguage(): LanguageInterface;
    public function setCreated(\DateTime $created): FieldTranslationInterface;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): FieldTranslationInterface;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
