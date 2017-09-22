<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\Collection;

interface SectionEntityInterface
{
    public function getHistory(): Collection;
    public function addHistory(SectionInterface $section): SectionEntityInterface;
    public function removeHistory(SectionInterface $section): SectionEntityInterface;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
