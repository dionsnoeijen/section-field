<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\Entity\LanguageInterface;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\LanguageConfig;

interface LanguageManagerInterface
{
    public function create(LanguageInterface $entity): LanguageInterface;
    public function read(Id $id): LanguageInterface;
    public function readAll(): array;
    public function update(): void;
    public function delete(LanguageInterface $entity): void;
    public function readByI18n(I18n $i18n): LanguageInterface;
    public function readByI18ns(array $i18ns): array;
    public function createByConfig(LanguageConfig $languageConfig): LanguageManagerInterface;
    public function updateByConfig(LanguageConfig $languageConfig): LanguageManagerInterface;
}
