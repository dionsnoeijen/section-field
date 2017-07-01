<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class Id
{
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        Assertion::integerish($id, 'For the id we need an integer');
        $this->id = $id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function create(int $id): self
    {
        return new self($id);
    }
}
