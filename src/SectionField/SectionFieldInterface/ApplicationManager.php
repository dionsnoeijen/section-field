<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\EntityInterface\Application;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\Id;

interface ApplicationManager
{
    public function create(Application $entity): Application;
    public function read(Id $id): Application;
    public function readAll(): array;
    public function update(): void;
    public function delete(Application $entity): void;
    public function createByConfig(ApplicationConfig $applicationConfig): Application;
    public function updateByConfig(ApplicationConfig $applicationConfig, Application $application): Application;
}
