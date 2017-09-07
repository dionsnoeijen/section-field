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
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const ORDER_BY = 'orderBy';

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

        if ($options['section'] instanceof FullyQualifiedClassName) {
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

        if ($this->options['section'] instanceof FullyQualifiedClassName) {
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

        return (int) $this->options['offset'];
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
            return (int) $this->options[self::LIMIT];
        } catch (InvalidArgumentException $exception) {
            return null;
        }
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
            return (array) $this->options[self::ORDER_BY];
        } catch (InvalidArgumentException $exception) {
            return null;
        }
    }

    public function getId(): ?Id
    {
        try {
            Assertion::keyIsset($this->options, self::ID, 'This id is not set');
            Assertion::digit($this->options[self::ID], 'The id is not numeric');

            return Id::fromInt($this->options[self::ID]);
        } catch (InvalidArgumentException $exception) {
            return null;
        }
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
