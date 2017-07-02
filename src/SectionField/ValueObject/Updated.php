<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

class Updated
{
    /**
     * @var \DateTime
     */
    private $updated;

    public function __construct(\DateTime $updated)
    {
        $this->updated = $updated;
    }

    public function __toString(): string
    {
        return $this->updated->format(\DateTime::ATOM);
    }

    public function getDateTime(): \DateTime
    {
        return $this->updated;
    }

    public static function create(\DateTime $updated): self
    {
        return new self($updated);
    }
}
