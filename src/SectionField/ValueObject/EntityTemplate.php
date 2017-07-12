<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

final class EntityTemplate
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
