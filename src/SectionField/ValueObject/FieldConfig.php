<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Tardigrades\Helper\StringConverter;

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
        Assertion::keyIsset($fieldConfig['field'], 'handle', 'Field needs a handle');
        Assertion::keyIsset($fieldConfig['field'], 'label', 'Field needs at least one label');
        Assertion::notEmpty($fieldConfig['field'], 'name', 'Field has no value');
        Assertion::notEmpty($fieldConfig['field'], 'handle', 'Field needs a handle');
        Assertion::isArray($fieldConfig['field'], 'label', 'Labels are to be defined in an array of available languages');

        $this->fieldConfig = $fieldConfig;
    }

    public function toArray(): array
    {
        return $this->fieldConfig;
    }

    public function getName(): string
    {
        return $this->fieldConfig['field']['name'];
    }

    public function getHandle(): string
    {
        return $this->fieldConfig['field']['handle'];
    }

    public function getMethodName(): string
    {
        $methodName = StringConverter::toCamelCase($this->fieldConfig['field']['handle']);

        return ucfirst($methodName);
    }

    public function getPropertyName(): string
    {
        return StringConverter::toCamelCase($this->fieldConfig['field']['handle']);
    }

    public function __toString(): string
    {
        $config = '';
        foreach ($this->fieldConfig['field'] as $key=>$value) {
            if (is_array($value)) {
                $config .= $key . ":\n";
                foreach ($value as $langValue) {
                    if (is_array($langValue)) {
                        $config .= ' -' . key($langValue) . ':' . array_shift($langValue) . "\n";
                    } else {
                        $config .= ' -' . $langValue . "\n";
                    }
                }
            } else {
                $config .= $key . ':' . $value . "\n";
            }
        }

        return $config;
    }

    public static function create(array $fieldConfig): self
    {
        return new self($fieldConfig);
    }
}
