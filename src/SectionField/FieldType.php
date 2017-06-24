<?php

namespace Tardigrades\SectionField;

abstract class FieldType implements SectionFieldInterface\FieldType {

    /** @var string */
    protected $name;

    /** @var \stdClass */
    protected $config;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): \stdClass
    {
        return $this->config;
    }
}
