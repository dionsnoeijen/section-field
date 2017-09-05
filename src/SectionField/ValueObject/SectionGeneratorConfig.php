<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Tardigrades\Helper\ArrayConverter;

final class SectionGeneratorConfig
{
    /**
     * @var array
     */
    private $sectionGeneratorConfig;

    private function __construct(array $sectionGeneratorConfig)
    {
        Assertion::keyExists($sectionGeneratorConfig,'generator', 'Config is not a section config');

        $this->sectionGeneratorConfig = $sectionGeneratorConfig;
    }

    public function toArray(): array
    {
        return $this->sectionGeneratorConfig;
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->sectionGeneratorConfig['generator']);
    }

    public static function create(array $sectionConfig): self
    {
        return new self($sectionConfig);
    }
}
