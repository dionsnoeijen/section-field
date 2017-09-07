<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class UpdateSectionCommand extends SectionCommand
{
    public function __construct(
        SectionManager $sectionManager
    ) {
        parent::__construct($sectionManager, 'sf:update-section');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Updates an existing section.')
            ->setHelp('This command allows you to update a section based on a yml section configuration. Pass along the path to a section configuration yml. Something like: section/blog.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $sections = $this->sectionManager->readAll();

        $this->renderTable($output, $sections, 'All installed Sections');
        $this->updateWhatRecord($input, $output);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $section = $this->getSection($input, $output);
        $config = $input->getArgument('config');

        try {
            $sectionConfig = SectionConfig::fromArray(
                Yaml::parse(
                    file_get_contents($config)
                )
            );
            $this->sectionManager->updateByConfig($sectionConfig, $section);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }

        $sections = $this->sectionManager->readAll();
        $this->renderTable($output, $sections, 'Section updated!');
    }
}

