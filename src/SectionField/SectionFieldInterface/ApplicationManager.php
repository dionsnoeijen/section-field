<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\EntityInterface\Application;
use Tardigrades\SectionField\ValueObject\Id;

interface ApplicationManager
{
    public function create(Application $entity): Application;
    public function read(Id $id): Application;
    public function readAll(): array;
    public function update(Application $entity): Application;
    public function delete(Application $entity): void;
}
