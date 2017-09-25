<?php
declare (strict_types=1);

namespace Example\Relationships\Entity;

use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class OtmMany
{
    /** @var \DateTime */
    protected $created;

    /** @var string */
    protected $title;

    /** @var \DateTime */
    protected $updated;

    /** @var string */
    protected $slug;

    /** @var OtmOne */
    protected $otmOne;

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

    public function setCreated(\DateTime $created): OtmMany
    {
        $this->created = $created;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): OtmMany
    {
        $this->title = $title;
        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): OtmMany
    {
        $this->updated = $updated;
        return $this;
    }

    public function getSlug(): ?Tardigrades\FieldType\Slug\ValueObject\Slug
    {
        if (!empty($this->slug)) {
            return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString($this->slug);
        }
        return null;
    }

    public function getOtmOne(): ?OtmOne
    {
        return $this->otmOne;
    }

    public function hasOtmOne(): bool
    {
        return !empty($this->otmOne);
    }

    public function setOtmOne(OtmOne $otmOne): OtmMany
    {
        $this->otmOne = $otmOne;
        return $this;
    }

    public function removeOtmOne(): OtmMany
    {
        $this->otmOne = null;
        return $this;
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

