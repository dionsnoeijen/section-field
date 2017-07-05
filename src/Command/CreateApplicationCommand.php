<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager;

class CreateApplicationCommand extends Command
{
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
            ->setHelp('Create a new application, an application is related to sections, you can group sections and isolate them when necessary. You need at least one application, and only one application counts as default.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Application');
    }
}
