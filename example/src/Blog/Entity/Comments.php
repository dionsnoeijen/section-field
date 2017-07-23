<?php
declare (strict_types=1);

namespace Example\Blog\Entity;

use Tardigrades;

class Comments
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $name;

    /** @var int */
    private $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Comments
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Comments
    {
        $this->name = $name;
        return $this;
    }

    public function onPrePersist(): void
    {
    }

    public function onPreUpdate(): void
    {
    }
}

