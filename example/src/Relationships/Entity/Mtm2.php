<?php
declare (strict_types=1);

namespace Example\Relationships\Entity;

use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Mtm2
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
    protected $mtm1s;

    /** @var int */
    private $id;

    public function __construct()
    {
        $this->mtm1s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): Mtm2
    {
        $this->created = $created;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): Mtm2
    {
        $this->title = $title;
        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): Mtm2
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

    public function getMtm1s(): Collection
    {
        return $this->mtm1s;
    }

    public function addMtm1(Mtm1 $mtm1): Mtm2
    {
        if ($this->mtm1s->contains($mtm1)) {
            return $this;
        }
        $this->mtm1s->add($mtm1);
        $mtm1->addMtm2($this);
        return $this;
    }

    public function removeMtm1(Mtm1 $mtm1): Mtm2
    {
        if (!$this->mtm1s->contains($mtm1)) {
            return $this;
        }
        $this->mtm1s->removeElement($mtm1);
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

