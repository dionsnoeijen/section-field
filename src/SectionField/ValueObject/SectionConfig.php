<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use Tardigrades\Helper\ArrayConverter;

final class SectionConfig
{
    /** @var array */
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
        return Name::fromString($this->sectionConfig['section']['name']);
    }

    public function getHandle(): Handle
    {
        return Handle::fromString($this->sectionConfig['section']['handle']);
    }

    public function getClassName(): ClassName
    {
        return ClassName::fromString($this->sectionConfig['section']['handle']);
    }

    public function getSlugField(): SlugField
    {
        try {
            Assertion::keyExists($this->sectionConfig['section'], 'slug', 'Slug is not defined');
            Assertion::notEmpty($this->sectionConfig['section']['slug'], 'The slug field must have a value');
            Assertion::string($this->sectionConfig['section']['slug'], 'The slug field must be a string');
            return SlugField::fromString($this->sectionConfig['section']['slug']);
        } catch (InvalidArgumentException $exception) {
            return SlugField::fromString('slug');
        }
    }

    public function getCreatedField(): CreatedField
    {
        try {
            Assertion::keyExists($this->sectionConfig['section'], 'created', 'Created is not defined');
            Assertion::notEmpty($this->sectionConfig['section']['created'], 'The created field must have a value');
            Assertion::string($this->sectionConfig['section']['created'], 'The created field must be a string');
            return CreatedField::fromString($this->sectionConfig['section']['created']);
        } catch (InvalidArgumentException $exception) {
            return CreatedField::fromString('created');
        }
    }

    public function getUpdatedField(): UpdatedField
    {
        try {
            Assertion::keyExists($this->sectionConfig['section'], 'updated', 'Updated is not defined');
            Assertion::notEmpty($this->sectionConfig['section']['created'], 'The updated field must have a value');
            Assertion::string($this->sectionConfig['section']['created'], 'The updated field must be a string');
            return UpdatedField::fromString($this->sectionConfig['section']['updated']);
        } catch (InvalidArgumentException $exception) {
            return UpdatedField::fromString('updated');
        }
    }

    public function getGeneratorConfig(): GeneratorConfig
    {
        return GeneratorConfig::fromArray($this->sectionConfig['section']);
    }

    public function getDefault(): string
    {
        return $this->sectionConfig['section']['default'];
    }

    public function getNamespace(): SectionNamespace
    {
        return SectionNamespace::fromString($this->sectionConfig['section']['namespace']);
    }

    public function getFullyQualifiedClassName(): FullyQualifiedClassName
    {
        return FullyQualifiedClassName::fromNamespaceAndClassName(
            $this->getNamespace(),
            $this->getClassName()
        );
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->sectionConfig['section']);
    }

    public static function fromArray(array $sectionConfig): self
    {
        return new self($sectionConfig);
    }
}
