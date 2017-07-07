<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\EntityInterface\Language;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\LanguageConfig;

interface LanguageManager
{
    public function create(Language $entity): Language;
    public function read(Id $id): Language;
    public function readAll(): array;
    public function update(): void;
    public function delete(Language $entity): void;
    public function readByI18n(I18n $i18n): Language;
    public function readByI18ns(array $i18ns): array;
    public function createByConfig(LanguageConfig $languageConfig): LanguageManager;
    public function updateByConfig(LanguageConfig $languageConfig): LanguageManager;
}
