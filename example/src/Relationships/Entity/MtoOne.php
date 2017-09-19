<?php
declare (strict_types=1);

namespace Example\Relationships\Entity;

use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MtoOne
{
    /** @var \DateTime */
    protected $created;

    /** @var string */
    protected $title;

    /** @var \DateTime */
    protected $updated;

    /** @var ArrayCollection */
    protected $mtoManies;

    /** @var int */
    private $id;

    public function __construct()
    {
        $this->mtoManies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): MtoOne
    {
        $this->created = $created;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): MtoOne
    {
        $this->title = $title;
        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): MtoOne
    {
        $this->updated = $updated;
        return $this;
    }

    public function getMtoManies(): Collection
    {
        return $this->mtoManies;
    }

    public function addMtoMany(MtoMany $mtoMany): MtoOne
    {
        if ($this->mtoManies->contains($mtoMany)) {
            return $this;
        }
        $this->mtoManies->add($mtoMany);
        $mtoMany->setMtoOne($this);
        return $this;
    }

    public function removeMtoMany(MtoMany $mtoMany): MtoOne
    {
        if (!$this->mtoManies->contains($mtoMany)) {
            return $this;
        }
        $this->mtoManies->removeElement($mtoMany);
        $mtoMany->removeMtoOne($this);
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
    }

    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime('now');
    }
}

