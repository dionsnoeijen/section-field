<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Tardigrades\Helper\StringConverter;

final class MethodName
{
    /**
     * @var string
     */
    private $methodName;

    private function __construct(string $methodName)
    {
        Assertion::string($methodName, 'The MethodName has to be a string');

        $this->methodName = $methodName;
    }

    public function __toString(): string
    {
        return $this->methodName;
    }

    public static function create(string $methodName): self
    {
        $methodName = StringConverter::toCamelCase($methodName);

        return new self(ucfirst($methodName));
    }
}
