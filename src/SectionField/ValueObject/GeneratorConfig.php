<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Tardigrades\Helper\ArrayConverter;

final class GeneratorConfig
{
    /** @var array */
    private $sectionGeneratorConfig;

    private function __construct(array $sectionGeneratorConfig)
    {
        Assertion::keyExists($sectionGeneratorConfig,'generator', 'Config is not a section config');

        $this->sectionGeneratorConfig = $sectionGeneratorConfig['generator'];
    }

    public function toArray(): array
    {
        return $this->sectionGeneratorConfig;
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->sectionGeneratorConfig['generator']);
    }

    public static function fromArray(array $sectionGeneratorConfig): self
    {
        return new self($sectionGeneratorConfig);
    }
}
