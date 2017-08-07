<?php

declare (strict_types=1);

namespace Tardigrades\FieldType\Slug\ValueObject;

use Assert\Assertion;

final class Slug
{
    /** @var array */
    private $slug;

    public function __construct(array $slug)
    {
        Assertion::notEmpty($slug, 'No slug elements defined');

        $this->slug = $slug;
    }

    public function toArray(): array
    {
        return $this->slug;
    }

    public function __toString(): string
    {
        return implode('-', $this->slug);
    }

    public static function create(array $slug): self
    {
        return new self($slug);
    }

    public static function fromString(string $slug): self
    {
        return new self([$slug]);
    }
}

