<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\EntityGenerator;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class GenerateSectionCommand extends SectionCommand
{
    /** @var EntityGenerator */
    private $entityGenerator;

    public function __construct(
        SectionManager $sectionManager,
        EntityGenerator $entityGenerator
    ) {
        $this->entityGenerator = $entityGenerator;

        parent::__construct($sectionManager, 'sf:generate-section');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a section.')
            ->setHelp('After creating a section, you can generate the accompanying files and tables (when using doctrine settings).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $sections = $this->sectionManager->readAll();
        $this->renderTable($output, $sections, 'Available sections.');
        $this->generateWhatSection($input, $output);
    }

    private function generateWhatSection(InputInterface $input, OutputInterface $output): void
    {
        $section = $this->getSection($input, $output);

        $this->entityGenerator->generateBySection($section);
    }
}
