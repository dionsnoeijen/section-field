<?php

namespace Tardigrades\SectionField\SectionFieldInterface;

interface StructureEntity {
    public function getName(): string;
    public function setName(string $name): string;
    public function getCreated(): \DateTime;
    public function setCreated(\DateTime $created): void;
    public function getUpdated(): \DateTime;
    public function setUpdated(\DateTime $updated): void;
}
