<?php

namespace Tardigrades\FieldType\FieldTypeInterface;

interface FieldType
{
    public function setName(string $name): void;
    public function getName(): string;
    public function getConfig(): \stdClass;
}
