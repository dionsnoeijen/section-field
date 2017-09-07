<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Tardigrades\Helper\ArrayConverter;

final class FieldMetadata
{
    /**
     * @var array
     */
    private $metadata;

    private function __construct(array $metadata)
    {
        Assertion::isArray($metadata, 'Metadata not defined');

        $this->metadata = $metadata;
    }

    public function toArray(): array
    {
        return $this->metadata;
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->metadata);
    }

    public static function fromArray(array $metadata): self
    {
        return new self($metadata);
    }
}
