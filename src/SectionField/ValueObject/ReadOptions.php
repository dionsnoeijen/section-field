<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use Tardigrades\FieldType\Slug\ValueObject\Slug;

final class ReadOptions
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

    /** @var array */
    private $options;

    private function __construct(
        array $options
    ) {

        $valid = false;
        if (is_array($options[self::SECTION])) {
            $valid = true;
        }

        if (is_string($options[self::SECTION])) {
            $valid = true;
        }

        if ($options[self::SECTION] instanceof FullyQualifiedClassName) {
            $valid = true;
        }

        if (!$valid) {
            throw new InvalidArgumentException('The section is not of a valid type', 400, null, $options[self::SECTION]);
        }

        $this->options = $options;
    }

    public function getSection(): array
    {
        $sectionEntities = [];

        if ($this->options[self::SECTION] instanceof FullyQualifiedClassName) {
            $sectionEntities = [$this->options[self::SECTION]];
        }

        if (is_string($this->options[self::SECTION])) {
            $sectionEntities = [FullyQualifiedClassName::fromString($this->options[self::SECTION])];
        }

        if (is_array($this->options[self::SECTION])) {
            foreach ($this->options[self::SECTION] as $section) {
                $sectionEntities[] = FullyQualifiedClassName::fromString((string) $section);
            }
        }

        return $sectionEntities;
    }

    public function getSectionId(): ?Id
    {
        try {
            Assertion::keyIsset($this->options, self::SECTION_ID,
                'The sectionId is not set'
            );
            Assertion::integerish($this->options[self::SECTION_ID],
                'The sectionId needs to be an integer'
            );
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return Id::fromInt($this->options[self::SECTION_ID]);
    }

    public function getOffset(): ?Offset
    {
        try {
            Assertion::keyIsset($this->options, self::OFFSET,
                'The offset is not set'
            );
            Assertion::integerish($this->options[self::OFFSET],
                'The offset needs to be an integer.'
            );
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return Offset::fromInt($this->options[self::OFFSET]);
    }

    public function getLimit(): ?Limit
    {
        try {
            Assertion::keyIsset($this->options, self::LIMIT,
                'The limit is not set'
            );
            Assertion::integerish($this->options[self::LIMIT],
                'The limit needs to be an integer.'
            );
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return Limit::fromInt($this->options[self::LIMIT]);
    }

    public function getOrderBy(): ?OrderBy
    {
        try {
            Assertion::keyIsset($this->options, self::ORDER_BY,
                'orderBy is not set'
            );
            Assertion::isArray($this->options[self::ORDER_BY],
                'Order by needs to be an array. Example: (["some" => "ASC"])'
            );
            $handle = Handle::fromString(key($this->options[self::ORDER_BY]));
            $sort = Sort::fromString(array_values($this->options[self::ORDER_BY])[0]);
            $orderBy = OrderBy::fromHandleAndSort($handle, $sort);
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return $orderBy;
    }

    public function getBefore(): ?Before
    {
        try {
            Assertion::keyIsset($this->options, self::BEFORE, 'Before is not defined');
            Assertion::string($this->options[self::BEFORE]);
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return Before::fromString($this->options[self::BEFORE]);
    }

    public function getAfter(): ?After
    {
        try {
            Assertion::keyIsset($this->options, self::AFTER, 'After is not defined');
            Assertion::string($this->options[self::AFTER]);
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return After::fromString($this->options[self::AFTER]);
    }

    public function getLocaleEnabled(): ?bool
    {
        try {
            Assertion::keyIsset($this->options, self::LOCALE_ENABLED, 'localeEnabled is not set');
            Assertion::boolean($this->options[self::LOCALE_ENABLED]);
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return (bool) $this->options[self::LOCALE_ENABLED];
    }

    public function getLocale(): ?string
    {
        try {
            Assertion::keyIsset($this->options, self::LOCALE, 'No locale defined');
            Assertion::string($this->options, 'Locale is supposed to be a string like en_EN');
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return (string) $this->options[self::LOCALE];
    }

    public function getSearch(): ?Search
    {
        try {
            Assertion::keyIsset($this->options, self::SEARCH, 'No search defined');
            Assertion::string($this->options, 'The search term must be a string');
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return Search::fromString($this->options[self::SEARCH]);
    }

    public function getField(): ?array
    {
        try {
            Assertion::isArray(
                $this->options[self::FIELD],
                'The field option must be an array. "fieldHandle" => "value"'
            );
            $field = [
                Handle::fromString(key($this->options[self::FIELD])),
                $this->options[self::FIELD][key($this->options[self::FIELD])]
            ];
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return $field;
    }

    public function getId(): ?Id
    {
        try {
            Assertion::keyIsset($this->options, self::ID, 'This id is not set');
            Assertion::digit($this->options[self::ID], 'The id is not numeric');
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return Id::fromInt($this->options[self::ID]);
    }

    public function getSlug(): ?Slug
    {
        try {
            Assertion::keyIsset($this->options, self::SLUG, 'The slug is not set');

            // There is a possibility the read options are built with a value object,
            // added flexibility by converting value to slug first.
            Assertion::string((string) $this->options[self::SLUG], 'The slug is supposed to be a string');

            return Slug::fromString((string) $this->options[self::SLUG]);
        } catch (InvalidArgumentException $exception) {
            return null;
        }
    }

    public static function fromArray(array $options): self
    {
        Assertion::isArray($options, 'Options must be an array');

        return new self($options);
    }
}
