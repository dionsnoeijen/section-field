<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;

class CreateApplicationCommand extends Command
{
    /** @var ApplicationManager */
    private $applicationManager;

    public function __construct(
        ApplicationManager $applicationManager
    ) {
        $this->applicationManager = $applicationManager;

        parent::__construct('sf:create-application');
    }

    protected function configure()
    {
        $this
            ->setDescription('Create application')
            ->setHelp('Create a new application, an application is related to sections, you can group sections and isolate them when necessary. You need at least one application, and only one application counts as default.')
            ->addArgument('config', InputArgument::REQUIRED, 'The language configuration yml');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getArgument('config');

        try {
            $applicationConfig = ApplicationConfig::create(
                Yaml::parse(file_get_contents($config))
            );
            $this->applicationManager->createByConfig($applicationConfig);
            $output->writeln('<info>Application created!</info>');
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid config. {$exception->getMessage()}</error>");
        }
    }
}
