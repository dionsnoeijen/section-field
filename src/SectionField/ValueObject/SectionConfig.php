<?php

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class SectionConfig
{
    /**
     * @var array
     */
    private $sectionConfig;

    private function __construct(array $sectionConfig)
    {
        Assertion::keyExists($sectionConfig,'section', 'Config is not a section config');
        Assertion::keyExists($sectionConfig['section'], 'name', 'The config contains no section name');
        Assertion::keyExists($sectionConfig['section'], 'handle', 'The config contains no section handle');
        Assertion::notEmpty($sectionConfig['section']['name'], 'The name is not defined');
        Assertion::notEmpty($sectionConfig['section']['handle'], 'The handle is not defined');
        Assertion::string($sectionConfig['section']['name'], 'The name must be a string');
        Assertion::string($sectionConfig['section']['handle'], 'The handle must be a string');
        Assertion::keyExists($sectionConfig['section'], 'fields', 'The config contains no fields');
        Assertion::isArray($sectionConfig['section']['fields'], 'Fields have to be defined as an arrauy');
        Assertion::keyExists($sectionConfig['section'], 'slug', 'You have to define what field(s) compose the slug');
        Assertion::keyExists($sectionConfig['section'], 'default', 'Assign a default field');

        $this->sectionConfig = $sectionConfig;
    }

    public function toArray(): array
    {
        return $this->sectionConfig;
    }

    public function getFields(): array
    {
        return $this->sectionConfig['section']['fields'];
    }

    public function getName(): string
    {
        return $this->sectionConfig['section']['name'];
    }

    public function getHandle(): string
    {
        return $this->sectionConfig['section']['handle'];
    }

    public function getClassName(): string
    {
        return ucfirst($this->sectionConfig['section']['handle']);
    }

    public function __toString(): string
    {
        $configText = '';
        foreach ($this->sectionConfig['section'] as $key=>$value) {
            $configText .= $key . ':';
            if (is_array($value)) {
                $configText .= "\n";
                foreach ($value as $subKey=>$subValue) {
                    $configText .= " - {$subValue}\n";
                }
                continue;
            }
            $configText .= $value . "\n";
        }

        return $configText;
    }

    public static function create(array $sectionConfig): self
    {
        return new self($sectionConfig);
    }
}
