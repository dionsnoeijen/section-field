<?php

namespace Tardigrades\FieldType\TextInput;

use Tardigrades\SectionField\SectionFieldInterface\FieldType;
use Tardigrades\SectionField\ValueObject\Text;

interface TextInputFieldType extends FieldType
{
    public function setText(Text $text): void;
    public function getText(): string;
}
