<?php
declare (strict_types=1);

namespace Example\Relationships\Entity;

use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MtoMany
{
    /** @var \DateTime */
    protected $created;

    /** @var string */
    protected $title;

    /** @var \DateTime */
    protected $updated;

    /** @var MtoOne */
    protected $mtoOne;

    /** @var string */
    protected $slug;

    /** @var int */
    private $id;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): MtoMany
    {
        $this->created = $created;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): MtoMany
    {
        $this->title = $title;
        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): MtoMany
    {
        $this->updated = $updated;
        return $this;
    }

    public function getMtoOne(): MtoOne
    {
        return $this->mtoOne;
    }

    public function setMtoOne(MtoOne $mtoOne): MtoMany
    {
        $this->mtoOne = $mtoOne;
        return $this;
    }

    public function removeMtoOne(MtoOne $mtoOne): MtoMany
    {
        $this->mtoOne = null;
        return $this;
    }

    public function getSlug(): ?Tardigrades\FieldType\Slug\ValueObject\Slug
    {
        if (!empty($this->slug)) {
            return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString($this->slug);
        }
        return null;
    }

    public function getDefault(): string
    {
        return $this->title;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new Assert\Length(['min' => '2','max' => '255']));
        $metadata->addPropertyConstraint('title', new Assert\NotBlank());
    }

    public function onPrePersist(): void
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
        $this->slug = Tardigrades\Helper\StringConverter::toSlug($this->getTitle() . '-' . $this->getCreated()->format('Y-m-d'));
    }

    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime('now');
    }
}

