<?php
declare (strict_types=1);

namespace Example\Relationships\Entity;

use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Mtm1
{
    /** @var \DateTime */
    protected $created;

    /** @var string */
    protected $title;

    /** @var \DateTime */
    protected $updated;

    /** @var string */
    protected $slug;

    /** @var ArrayCollection */
    protected $mtm2s;

    /** @var int */
    private $id;

    public function __construct()
    {
        $this->mtm2s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): Mtm1
    {
        $this->created = $created;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): Mtm1
    {
        $this->title = $title;
        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): Mtm1
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

    public function getMtm2s(): Collection
    {
        return $this->mtm2s;
    }

    public function addMtm2(Mtm2 $mtm2): Mtm1
    {
        if ($this->mtm2s->contains($mtm2)) {
            return $this;
        }
        $this->mtm2s->add($mtm2);
        $mtm2->addMtm1($this);
        return $this;
    }

    public function removeMtm2(Mtm2 $mtm2): Mtm1
    {
        if (!$this->mtm2s->contains($mtm2)) {
            return $this;
        }
        $this->mtm2s->removeElement($mtm2);
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

