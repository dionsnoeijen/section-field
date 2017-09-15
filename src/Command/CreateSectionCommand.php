<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class CreateSectionCommand extends SectionCommand
{
    public function __construct(
        SectionManagerInterface $sectionManager
    ) {
        parent::__construct($sectionManager, 'sf:create-section');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new section.')
            ->setHelp('This command allows you to create a section based on a yml section configuration. Pass along the path to a section configuration yml. Something like: section/blog.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $config = $input->getArgument('config');

        try {
            if (file_exists($config)) {
                $parsed = Yaml::parse(file_get_contents($config));
                if (is_array($parsed)) {
                    $sectionConfig = SectionConfig::fromArray($parsed);
                    $this->sectionManager->createByConfig($sectionConfig);
                    $output->writeln('<info>Section created!</info>');
                    return;
                }
            }
            throw new \Exception('No valid config found.');
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
        }
    }
}
