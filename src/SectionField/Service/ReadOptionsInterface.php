<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

interface ReadOptionsInterface
{
    public static function fromArray(array $options): ReadOptionsInterface;
    public function toArray(): array;
}
