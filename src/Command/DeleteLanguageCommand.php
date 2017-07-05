<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager;

class DeleteLanguageCommand extends Command
{
    private $applicationManager;

    public function __construct(
        ApplicationManager $applicationManager
    ) {
        $this->applicationManager = $applicationManager;

        parent::__construct('sf:delete-language');
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete language')
            ->setHelp('Delete language');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Language');
    }
}
