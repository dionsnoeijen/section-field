<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager;
use Tardigrades\SectionField\Service\ApplicationNotFoundException;

class ListApplicationCommand extends ApplicationCommand
{
    private $applicationManager;

    public function __construct(
        ApplicationManager $applicationManager
    ) {
        $this->applicationManager = $applicationManager;

        parent::__construct('sf:list-application');
    }

    protected function configure()
    {
        $this
            ->setDescription('Show applications')
            ->setHelp('Show applications');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $applications = $this->applicationManager->readAll();
            $this->renderTable($output, $applications, 'All installed Applications');
        } catch (ApplicationNotFoundException $exception) {
            $output->writeln('No applications found');
        }
    }
}
