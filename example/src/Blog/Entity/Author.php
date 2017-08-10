<?php
declare (strict_types=1);

namespace Example\Blog\Entity;

use Tardigrades;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Author
{
    /** @var \DateTime */
    protected $created;

    /** @var string */
    protected $name;

    /** @var \DateTime */
    protected $updated;

    /** @var string */
    protected $authorSlug;

    /** @var ArrayCollection */
    protected $blogs;

    /** @var int */
    private $id;

    public function __construct()
    {
        $this->blogs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): Author
    {
        $this->created = $created;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Author
    {
        $this->name = $name;
        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): Author
    {
        $this->updated = $updated;
        return $this;
    }

    public function getAuthorSlug(): ?Tardigrades\FieldType\Slug\ValueObject\Slug
    {
        if (!empty($this->authorSlug)) {
            return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString($this->authorSlug);
        }
        return null;
    }

    public function getBlogs(): Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): Author
    {
        if ($this->blogs->contains($blog)) {
            return $this;
        }
        $this->blogs->add($blog);
        $blog->addAuthor($this);
        return $this;
    }

    public function removeBlog(Blog $blog): Author
    {
        if (!$this->blogs->contains($blog)) {
            return $this;
        }
        $this->blogs->removeElement($blog);
        return $this;
    }

    public function getSlug(): Tardigrades\FieldType\Slug\ValueObject\Slug
    {
        return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString($this->authorSlug);
    }

    public function onPrePersist(): void
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
        $this->authorSlug = Tardigrades\Helper\StringConverter::toSlug($this->getName());
    }

    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime('now');
    }
}

