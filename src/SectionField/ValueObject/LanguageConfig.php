<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class LanguageConfig
{
    /**
     * @var array
     */
    private $languageConfig;

    private function __construct(array $languageConfig)
    {
        Assertion::keyIsset($languageConfig, 'language', 'Config is not a language config');
        Assertion::isArray($languageConfig['language'], 'The languages should consist of an array');

        $this->languageConfig = $languageConfig;
    }

    public function toArray(): array
    {
        return $this->languageConfig;
    }

    public function __toString(): string
    {
        $config = '';
        foreach ($this->languageConfig['language'] as $value) {
            $config .= $value . "\n";
        }

        return $config;
    }

    public static function create(array $languageConfig): self
    {
        return new self($languageConfig);
    }
}
