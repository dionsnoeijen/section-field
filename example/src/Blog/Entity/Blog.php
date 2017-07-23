<?php
declare (strict_types=1);

namespace Example\Blog\Entity;

use Tardigrades;

class Blog
{
    /** @var \DateTime */
    protected $created;

    /** @var string */
    protected $subtitle;

    /** @var string */
    protected $title;

    /** @var \DateTime */
    protected $updated;

    /** @var string */
    protected $blogSlug;

    /** @var int */
    private $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): Blog
    {
        $this->created = $created;
        return $this;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): Blog
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Blog
    {
        $this->title = $title;
        return $this;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): Blog
    {
        $this->updated = $updated;
        return $this;
    }

    public function getBlogSlug(): Tardigrades\FieldType\Slug\ValueObject\Slug
    {
        return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString($this->blogSlug);
    }

    public function getSlug(): Tardigrades\FieldType\Slug\ValueObject\Slug
    {
        return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString($this->blogSlug);
    }

    public function onPrePersist(): void
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
        $this->blogSlug = Tardigrades\Helper\StringConverter::toSlug($this->getTitle() . '-' . $this->getCreated()->format('Y-m-d'));
    }

    public function onPreUpdate(): void
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
    }
}

