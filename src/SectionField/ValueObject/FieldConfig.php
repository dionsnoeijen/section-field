<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class FieldConfig
{
    /**
     * @var array
     */
    private $fieldConfig;

    private function __construct(array $fieldConfig)
    {
        Assertion::keyIsset($fieldConfig, 'field', 'Config is not a field config');
        Assertion::keyIsset($fieldConfig['field'], 'name', 'No name in config');
        Assertion::notEmpty($fieldConfig['field'], 'name', 'Field has no value');

        $this->fieldConfig = $fieldConfig;
    }

    public function toArray(): array
    {
        return $this->fieldConfig;
    }

    public function __toString(): string
    {
        $config = '';
        foreach ($this->fieldConfig['field'] as $key=>$value) {
            $config .= $key . ':' . $value . "\n";
        }

        return $config;
    }

    public static function create(array $fieldConfig): self
    {
        return new self($fieldConfig);
    }
}
