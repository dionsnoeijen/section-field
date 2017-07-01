<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

class Created
{
    /**
     * @var \DateTime
     */
    private $created;

    public function __construct(\DateTime $created)
    {
        $this->created = $created;
    }

    public function __toString(): string
    {
        return $this->created->format(\DateTime::ATOM);
    }

    public static function create(\DateTime $created): self
    {
        return new self($created);
    }
}
