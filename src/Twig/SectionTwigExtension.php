<?php
declare (strict_types=1);

namespace Tardigrades\Twig;

use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\Service\DoctrineReadOptions; // This will change later on, to use a more flexible method to have read options tied to specific data sources.
use Twig_Extension;
use Twig_Function;

class SectionTwigExtension extends Twig_Extension
{
    /** @var ReadSection */
    private $readSection;

    /** @var array */
    private $options;

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

    /**
     * Returns entries that belong to a specific section.
     * In the future it will be possible to fetch entries of
     * multiple sections at once.
     *
     * A section can be passed by it's FQCN: Fully.Qualified.Example
     * Or by it's handle: example
     *
     * @param string $section
     * @return SectionTwigExtension
     */
    public function section(string $section): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::SECTION] = $section;

        return $this;
    }

    /**
     * Limit returned entries
     *
     * @param int $limit
     * @return SectionTwigExtension
     */
    public function limit(int $limit): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::LIMIT] = $limit;

        return $this;
    }

    /**
     * Offset returned entries
     *
     * @param int $offset
     * @return SectionTwigExtension
     */
    public function offset(int $offset): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::OFFSET] = $offset;

        return $this;
    }

    /**
     * Specify which field to order by
     *
     * @param array $orderBy
     * @return SectionTwigExtension
     */
    public function orderBy(array $orderBy): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::ORDER_BY] = $orderBy;

        return $this;
    }

    /**
     * Specify sort direction
     *
     * ASC|DESC
     *
     * @param string $sort
     * @return SectionTwigExtension
     */
    public function sort(string $sort): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::SORT] = $sort;

        return $this;
    }

    /**
     * Only fetch entries with a Post Date that is before the given date.
     *
     * @param string $before
     * @return SectionTwigExtension
     */
    public function before(string $before): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::BEFORE] = new \DateTime($before);

        return $this;
    }

    /**
     * Only fetch entries with a Post Date that is on or after the given date.
     *
     * @param string $after
     * @return SectionTwigExtension
     */
    public function after(string $after): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::AFTER] = new \DateTime($after);

        return $this;
    }

    /**
     * Provides ability to get entries that are for another locale. (v1.1)
     *
     * @param bool $enabled
     * @return SectionTwigExtension
     */
    public function localeEnabled(bool $enabled = true): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::LOCALE_ENABLED] = $enabled;

        return $this;
    }

    /**
     * Fetch just the entries for a specific locale.
     *
     * @param string $locale
     * @return SectionTwigExtension
     */
    public function locale(string $locale): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::LOCALE] = $locale;

        return $this;
    }

    /**
     * Only fetch the entry with the given ID.
     *
     * @param int $id
     * @return SectionTwigExtension
     */
    public function id(int $id): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::ID] = $id;

        return $this;
    }

    /**
     * Only fetch the entry with the given slug.
     *
     * @param string $slug
     * @return SectionTwigExtension
     */
    public function slug(string $slug): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::SLUG] = $slug;

        return $this;
    }

    /**
     * This method requires some extra thinking.
     * How are we going to handle searching in the context of
     * SexyField intelligently? What version of SexyField will
     * this be available?
     *
     * @param string $searchQuery
     * @return SectionTwigExtension
     */
    public function search(string $searchQuery): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::SEARCH] = $searchQuery;

        return $this;
    }

    /**
     * Return entries based on a field value.
     * Example, based on a title with a value: "Awesome Title"
     *
     * @param string $fieldHandle
     * @param string $value
     * @return SectionTwigExtension
     */
    public function field(string $fieldHandle, string $value): SectionTwigExtension
    {
        $this->options[DoctrineReadOptions::FIELD] = [$fieldHandle=>$value];

        return $this;
    }

    /**
     * @param array $options
     * @return \ArrayIterator
     */
    public function fetch(array $options = []): \ArrayIterator
    {
        $options = array_merge($this->options, $options);

        return $this->readSection->read(DoctrineReadOptions::fromArray($options));
    }
}
