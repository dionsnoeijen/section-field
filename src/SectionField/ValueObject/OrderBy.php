<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class OrderBy
{
    /** @var array */
    private $orderBy;

    private function __construct(array $orderBy)
    {
        Assertion::isArray($orderBy, 'OrderBy should be an array ["fieldHandle"=>"ASC|DESC"]');

        $this->orderBy = $orderBy;
    }

    public function toArray(): array
    {
        return $this->orderBy;
    }

    public static function fromHandleAndSort(Handle $handle, Sort $sort): self
    {
        return new self([(string) $handle => (string) $sort]);
    }
}
