<?php

namespace Tardigrades\SectionField\SectionFieldInterface;

interface FieldType
{
    public function setName(string $name): void;
    public function getName(): string;
    public function getConfig(): \stdClass;
}
