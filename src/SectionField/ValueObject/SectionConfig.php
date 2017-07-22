<?php
declare (strict_types=1);

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
        Assertion::keyExists($sectionConfig['section'], 'default', 'Assign a default field');
        Assertion::keyExists($sectionConfig['section'], 'namespace', 'We do need a namespace');
        Assertion::string($sectionConfig['section']['namespace'], 'namespace', 'Namespace is not a string');
        Assertion::notEmpty($sectionConfig['section']['namespace'], 'The namespace value should not be empty');

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

    public function getName(): Name
    {
        return Name::create($this->sectionConfig['section']['name']);
    }

    public function getHandle(): Handle
    {
        return Handle::create($this->sectionConfig['section']['handle']);
    }

    public function getClassName(): ClassName
    {
        return ClassName::fromString($this->sectionConfig['section']['handle']);
    }

    public function getSlugField(): SlugField
    {
        Assertion::notEmpty($this->sectionConfig['section']['slug'], 'The slug field must have a value');
        Assertion::string($this->sectionConfig['section']['slug'], 'The slug field must be a string');

        return SlugField::fromString($this->sectionConfig['section']['slug']);
    }

    public function getNamespace(): SectionNamespace
    {
        return SectionNamespace::fromString($this->sectionConfig['section']['namespace']);
    }

    public function __toString(): string
    {
        $configText = '';
        foreach ($this->sectionConfig['section'] as $key=>$value) {
            $configText .= $key . ':';
            if (is_array($value)) {
                $configText .= PHP_EOL;
                foreach ($value as $subKey=>$subValue) {
                    $configText .= " - {$subValue}" . PHP_EOL;
                }
                continue;
            }
            $configText .= $value . PHP_EOL;
        }

        return $configText;
    }

    public static function create(array $sectionConfig): self
    {
        return new self($sectionConfig);
    }
}
