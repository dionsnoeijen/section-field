<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\EntityInterface\Language;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;

interface LanguageManager
{
    public function create(Language $entity): Language;
    public function read(Id $id): Language;
    public function readAll(): array;
    public function update(Language $entity): Language;
    public function delete(Language $entity): void;
    public function readByI18n(I18n $i18n): Language;
}
