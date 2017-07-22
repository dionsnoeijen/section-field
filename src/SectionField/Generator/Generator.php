<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\SectionField\SectionFieldInterface\Generator as GeneratorInterface;

class Generator implements GeneratorInterface
{
    /** @var array */
    private $generators;

    /** @var array */
    private $buildMessages = [];

    public function __construct(array $generators)
    {
        $this->generators = $generators;
    }

    public function generateBySection(Section $section): void
    {
        /** @var GeneratorInterface $generator */
        foreach ($this->generators as $generator) {
            $generator->generateBySection($section);
            $this->buildMessages += $generator->getBuildMessages();
        }
    }

    public function getBuildMessages(): array
    {
        return $this->buildMessages;
    }
}
