<?php
declare (strict_types=1);

namespace Example\Blog\Entity;

use Tardigrades;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Blog
{
    /** @var string */
    protected $body;

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

    /** @var ArrayCollection */
    protected $comments;

    /** @var int */
    private $id;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): Blog
    {
        $this->body = $body;
        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): Blog
    {
        $this->created = $created;
        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): Blog
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): Blog
    {
        $this->title = $title;
        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): Blog
    {
        $this->updated = $updated;
        return $this;
    }

    public function getBlogSlug(): ?Tardigrades\FieldType\Slug\ValueObject\Slug
    {
        if (!empty($this->blogSlug)) {
            return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString($this->blogSlug);
        }
        return null;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): Blog
    {
        if ($this->comments->contains($comment)) {
            return $this;
        }
        $this->comments->add($comment);
        $comment->setBlog($this);
        return $this;
    }

    public function removeComment(Comment $comment): Blog
    {
        if (!$this->comments->contains($comment)) {
            return $this;
        }
        $this->comments->removeElement($comment);
        $comment->removeBlog($this);
        return $this;
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
        $this->updated = new \DateTime('now');
    }
}

