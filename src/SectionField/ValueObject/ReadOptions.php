<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use Tardigrades\FieldType\Slug\ValueObject\Slug;

class ReadOptions
{
    const ID = 'id';
    const SLUG = 'slug';
    const SECTION = 'section';
    const SECTION_ID = 'sectionId';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const ORDER_BY = 'orderBy';
    const SORT = 'sort';
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';
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

    public function getSectionId(): ?int
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

        return (int) $this->options[self::SECTION_ID];
    }

    public function getOffset(): ?int
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

        return (int) $this->options[self::OFFSET];
    }

    public function getLimit(): ?int
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

        return (int) $this->options[self::LIMIT];
    }

    public function getOrderBy(): ?array
    {
        try {
            Assertion::keyIsset($this->options, self::ORDER_BY,
                'orderBy is not set'
            );
            Assertion::isArray($this->options[self::ORDER_BY],
                'Order by needs to be an array. Example: (["some" => "ASC"])'
            );
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return (array) $this->options[self::ORDER_BY];
    }

    /**
     * @todo: Probably unnecessary method
     * @return null|string
     */
    public function getSort(): ?string
    {
        try {
            Assertion::choice($this->options[self::SORT], [self::SORT_ASC, self::SORT_DESC], 'Sort option incorrect');
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return (string) $this->options[self::SORT];
    }

    public function getBefore(): ?\DateTime
    {
        try {
            Assertion::keyIsset($this->options, self::BEFORE, 'Before is not defined');
            Assertion::string($this->options[self::BEFORE]);
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return new \DateTime($this->options[self::BEFORE]);
    }

    public function getAfter(): ?\DateTime
    {
        try {
            Assertion::keyIsset($this->options, self::AFTER, 'After is not defined');
            Assertion::string($this->options[self::AFTER]);
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return new \DateTime($this->options[self::AFTER]);
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
            Assertion::keyIsset($this->options, self::LOCAlE, 'No locale defined');
            Assertion::string($this->options, 'Locale is supposed to be a string like en_EN');
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return (string) $this->options[self::LOCALE];
    }

    public function getSearch(): ?string
    {
        try {
            Assertion::keyIsset($this->options, self::SEARCH, 'No search defined');
            Assertion::string($this->options, 'The search term must be a string');
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return (string) $this->options[self::SEARCH];
    }

    public function field(): ?Handle
    {
        try {
            $handle = Handle::fromString($this->options[self::FIELD]);
        } catch (InvalidArgumentException $exception) {
            return null;
        }

        return $handle;
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
        return new self($options);
    }
}
