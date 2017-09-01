<?php

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\Service\FieldNotFoundException;

class ListFieldCommand extends FieldCommand
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(
        FieldManager $fieldManager
    ) {
        $this->fieldManager = $fieldManager;

        parent::__construct('sf:list-field');
    }

    protected function configure()
    {
        $this
            ->setDescription('Show installed fields.')
            ->setHelp('This command lists all installed fields.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $fields = $this->fieldManager->readAll();
            $this->renderTable($output, $fields, 'All installed Fields');
        } catch (FieldNotFoundException $exception) {
            $output->writeln('No fields found');
        }
    }
}
