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

    public function getName(): Name
    {
        return Name::create($this->fieldConfig['field']['name']);
    }

    public function getHandle(): Handle
    {
        return Handle::create($this->fieldConfig['field']['handle']);
    }

    public function getMethodName(): MethodName
    {
        return MethodName::create($this->fieldConfig['field']['handle']);
    }

    public function getTypeConfig(): array
    {
        Assertion::keyIsset($this->fieldConfig['field'], 'typeConfig', 'No typeConfig defined');
        Assertion::notEmpty($this->fieldConfig['field']['typeConfig'], 'Type config is empty');
        Assertion::isArray($this->fieldConfig['field']['typeConfig'], 'Type config is not an array');

        return $this->fieldConfig['field']['typeConfig'];
    }

    public function getPropertyName(): PropertyName
    {
        return PropertyName::create($this->fieldConfig['field']['handle']);
    }

    public function getEntityEvents(): array
    {
        Assertion::keyExists($this->fieldConfig['field'], 'entityEvents', 'Entity events not defined');
        Assertion::isArray($this->fieldConfig['field']['entityEvents'], 'Entity events should be an array of events you want a generator to run for.');

        return $this->fieldConfig['field']['entityEvents'];
    }

    public function __toString(): string
    {
        $config = '';
        foreach ($this->fieldConfig['field'] as $key=>$value) {
            if (is_array($value)) {
                $config .= $key . ':' . PHP_EOL;
                foreach ($value as $langValue) {
                    if (is_array($langValue)) {
                        $config .= ' -' . key($langValue) . ':' . array_shift($langValue) . "\n";
                    } else {
                        $config .= ' -' . $langValue . PHP_EOL;
                    }
                }
            } else {
                $config .= $key . ':' . $value . PHP_EOL;
            }
        }

        return $config;
    }

    public static function create(array $fieldConfig): self
    {
        return new self($fieldConfig);
    }
}
