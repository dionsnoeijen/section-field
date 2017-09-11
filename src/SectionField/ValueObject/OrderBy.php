<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

final class OrderBy
{
    /** @var Handle */
    private $handle;

    /** @var Sort */
    private $sort;

    private function __construct(Handle $handle, Sort $sort)
    {
        $this->handle = $handle;
        $this->sort = $sort;
    }

    public function toArray(): array
    {
        return [(string) $this->handle => (string) $this->sort];
    }

    public function getHandle(): Handle
    {
        return $this->handle;
    }

    public function getSort(): Sort
    {
        return $this->sort;
    }

    public static function fromHandleAndSort(Handle $handle, Sort $sort): self
    {
        return new self($handle, $sort);
    }
}
