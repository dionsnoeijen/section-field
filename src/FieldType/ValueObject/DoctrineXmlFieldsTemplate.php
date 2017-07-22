<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

final class DoctrineXmlFieldsTemplate
{
    /** @var string */
    private $template;

    private function __construct(string $template)
    {
        $this->template = $template;
    }

    public function __toString(): string
    {
        return $this->template;
    }

    public static function create(string $template): self
    {
        return new self($template);
    }
}
