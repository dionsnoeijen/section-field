<?php

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;

class ListFieldCommand extends Command
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
        $fields = $this->fieldManager->readAll();

        $this->renderTable($output, $fields);
    }

    private function renderTable(OutputInterface $output, array $fields)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($fields as $field) {
            $rows[] = [
                $field->getId(),
                $field->getName(),
                $field->getHandle(),
                $field->getFieldType()->getType(),
                (string) $field->getConfig(),
                (string) $field->getCreated(),
                (string) $field->getUpdated()
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed Fields</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'type', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
