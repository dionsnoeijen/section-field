<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\FieldType\Slug\ValueObject\Slug;
use Tardigrades\SectionField\ValueObject\After;
use Tardigrades\SectionField\ValueObject\Before;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Limit;
use Tardigrades\SectionField\ValueObject\Offset;
use Tardigrades\SectionField\ValueObject\OrderBy;

abstract class ReadOptions
{
    const ID = 'id';
    const SLUG = 'slug';
    const SECTION = 'section';
    const SECTION_ID = 'sectionId';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const ORDER_BY = 'orderBy';
    const SORT = 'sort';
    const BEFORE = 'before';
    const AFTER = 'after';
    const LOCALE_ENABLED = 'localeEnabled';
    const LOCALE = 'locale';
    const SEARCH = 'search';
    const FIELD = 'field';

    abstract public function getSection(): array;
    abstract public function getSectionId(): ?Id;
    abstract public function getOffset(): ?Offset;
    abstract public function getLimit(): ?Limit;
    abstract public function getOrderBy(): ?OrderBy;
    abstract public function getBefore(): ?Before;
    abstract public function getAfter(): ?After;
    abstract public function getLocaleEnabled(): ?bool;
    abstract public function getLocale(): ?string;
    abstract public function getId(): ?Id;
    abstract public function getSlug(): ?Slug;
    abstract public function toArray(): array;
    abstract public static function fromArray(array $options): ReadOptions;
}
