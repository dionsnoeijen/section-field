<?php

namespace Tardigrades\FieldType\TextInput;

use Assert\Assertion;
use Tardigrades\SectionField\FieldType;
use Tardigrades\SectionField\ValueObject\Text;

class TextInput extends FieldType implements TextInputFieldType
{
    /** @var Text */
    private $text;

    public function __construct(Text $text = null)
    {
        Assertion::nullOrNotBlank($text, 'Text should not be blank');
        Assertion::length($text, $this->getConfig()->length);
        $this->text = $text;
    }

    public function setText(Text $text = null): void
    {
        Assertion::nullOrNotBlank($text, 'Text should not be blank');
        Assertion::length($text, $this->getConfig()->length);
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return (string) $this->text;
    }
}
