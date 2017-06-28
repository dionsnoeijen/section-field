<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\Service\FieldTypeManager;

class InstallFieldTypeCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    public function __construct(
        EntityManager $entityManager,
        FieldTypeManager $fieldTypeManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldTypeManager = $fieldTypeManager;

        parent::__construct('sf:install-field-type');
    }

    protected function configure()
    {
        $this
            ->setDescription('Install a field type. Escape the backslash! Like so: This\\\Is\\\Namespace')
            ->setHelp('This command installs a field type, just give the namespace where to find the field.')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Field type namespace')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $input->getArgument('namespace');

        $fieldType = $this->fieldTypeManager->createWithNamespace($namespace);

        $this->renderTable($output, [$fieldType]);
    }

    private function renderTable(OutputInterface $output, array $fieldTypes)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($fieldTypes as $fieldType) {
            $rows[] = [
                $fieldType->getId(),
                $fieldType->getType(),
                $fieldType->getNamespace(),
                $fieldType->getCreated()->format('Y-m-d'),
                $fieldType->getUpdated()->format('Y-m-d')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>FieldType installed</info>', array('colspan' => 5))
        ];

        $table
            ->setHeaders(['#id', 'type', 'namespace', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
