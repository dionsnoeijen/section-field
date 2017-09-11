<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class Before
{
    /** @var \DateTimeInterface */
    private $dateTime;

    private function __construct(\DateTimeInterface $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function __toString()
    {
        return $this->dateTime->format('D-m-y h:i');
    }

    public static function fromString(string $dateTime): self
    {
        $format = 'D-m-y h:i';
        Assertion::date($dateTime, $format);
        $dateTime = \DateTimeImmutable::createFromFormat($format, $dateTime);

        return new self($dateTime);
    }

    public static function fromDateTime(\DateTime $dateTime): self
    {
        return new self(\DateTimeImmutable::createFromMutable($dateTime));
    }
}
