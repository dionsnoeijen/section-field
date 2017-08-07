<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use Tardigrades\FieldType\Slug\ValueObject\Slug;

class ReadOptions
{
    /** @var array */
    private $options;

    private function __construct(
        array $options
    ) {
        try {
            Assertion::isArray($options['section'],
                'Section must be passed in array as entity.'
            );
        } catch (InvalidArgumentException $exception) {
            Assertion::string($options['section'],
                'Section must be passed as an array or string.'
            );
        }

        $this->options = $options;
    }

    public function getSection(): array
    {
        $sectionEntities = [];

        if (is_string($this->options['section'])) {
            $sectionEntities = [FullyQualifiedClassName::create($this->options['section'])];
        }

        if (is_array($this->options['section'])) {
            foreach ($this->options['section'] as $section) {
                $sectionEntities[] = FullyQualifiedClassName::create($section);
            }
        }

        return $sectionEntities;
    }

    public function getOffset(): ?int
    {
        try {
            Assertion::keyIsset($this->options, 'offset',
                'The offset is not set'
            );
            Assertion::integerish($this->options['offset'],
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
            Assertion::keyIsset($this->options, 'limit',
                'The limit is not set'
            );
            Assertion::integerish($this->options['limit'],
                'The limit needs to be an integer.'
            );
            return (int) $this->options['limit'];
        } catch (InvalidArgumentException $exception) {
            return null;
        }
    }

    public function getOrderBy(): ?array
    {
        try {
            Assertion::keyIsset($this->options, 'orderBy',
                'orderBy is not set'
            );
            Assertion::isArray($this->options['orderBy'],
                'Order by needs to be an array. Example: (["some" => "ASC"])'
            );
            return (array) $this->options['orderBy'];
        } catch (InvalidArgumentException $exception) {
            return null;
        }
    }

    public function getSlug(): ?Slug
    {
        try {
            Assertion::keyIsset($this->options, 'slug', 'The slug is not set');
            Assertion::string($this->options['slug'], 'The slug is supposed to be a string');

            return Slug::fromString($this->options['slug']);
        } catch (InvalidArgumentException $exception) {
            return null;
        }
    }

    public static function fromArray(array $options): self
    {
        return new self($options);
    }
}
