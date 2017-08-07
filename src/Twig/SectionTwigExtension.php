<?php
declare (strict_types=1);

namespace Tardigrades\Twig;

use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\ValueObject\ReadOptions;
use Twig_Extension;
use Twig_Function;

class SectionTwigExtension extends Twig_Extension
{
    /** @var ReadSection */
    private $readSection;

    /** @var array */
    private $options;

    const SECTION = 'section';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const ORDER_BY = 'orderBy';

    public function __construct(ReadSection $readSection)
    {
        $this->readSection = $readSection;
    }

    public function getFunctions(): array
    {
        return array(
            new Twig_Function('section', array($this, 'section'))
        );
    }

    public function section(string $section): SectionTwigExtension
    {
        $this->options[self::SECTION] = $section;

        return $this;
    }

    public function limit(int $limit): SectionTwigExtension
    {
        $this->options[self::LIMIT] = $limit;

        return $this;
    }

    public function offset(int $offset): SectionTwigExtension
    {
        $this->options[self::OFFSET] = $offset;

        return $this;
    }

    public function orderBy(array $orderBy): SectionTwigExtension
    {
        $this->options[self::ORDER_BY] = $orderBy;

        return $this;
    }

    public function fetch(array $options = []): \ArrayIterator
    {
        $options = array_merge($this->options, $options);

        return $this->readSection->read(ReadOptions::fromArray($options));
    }
}
