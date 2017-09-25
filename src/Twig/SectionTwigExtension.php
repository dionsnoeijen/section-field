<?php
declare (strict_types=1);

namespace Tardigrades\Twig;

use Tardigrades\SectionField\Service\EntryNotFoundException;
use Tardigrades\SectionField\Service\ReadOptions;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Twig_Extension;
use Twig_Function;

class SectionTwigExtension extends Twig_Extension
{
    /** @var ReadSectionInterface */
    private $readSection;

    /** @var array */
    private $options;

    /** @var bool */
    private $throwNotFound = false;

    public function __construct(ReadSectionInterface $readSection)
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
        $this->options[ReadOptions::SECTION] = $section;

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
        $this->options[ReadOptions::LIMIT] = $limit;

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
        $this->options[ReadOptions::OFFSET] = $offset;

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
        $this->options[ReadOptions::ORDER_BY] = $orderBy;

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
        $this->options[ReadOptions::SORT] = $sort;

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
        $this->options[ReadOptions::BEFORE] = new \DateTime($before);

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
        $this->options[ReadOptions::AFTER] = new \DateTime($after);

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
        $this->options[ReadOptions::LOCALE_ENABLED] = $enabled;

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
        $this->options[ReadOptions::LOCALE] = $locale;

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
        $this->throwNotFound = true;
        $this->options[ReadOptions::ID] = $id;

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
        $this->throwNotFound = true;
        $this->options[ReadOptions::SLUG] = $slug;

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
        $this->options[ReadOptions::SEARCH] = $searchQuery;

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
        $this->options[ReadOptions::FIELD] = [$fieldHandle=>$value];

        return $this;
    }

    /**
     * @param array $options
     * @return \ArrayIterator
     */
    public function fetch(array $options = []): \ArrayIterator
    {
        $options = array_merge($this->options, $options);

        if (!$this->throwNotFound) {
            try {
                $entries = $this->readSection->read(ReadOptions::fromArray($options));
            } catch (EntryNotFoundException $exception) {
                $entries = new \ArrayIterator();
            }
        } else {
            $entries = $this->readSection->read(ReadOptions::fromArray($options));
        }

        return $entries;
    }
}
