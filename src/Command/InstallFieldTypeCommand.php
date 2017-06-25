<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Tardigrades\Entity\FieldType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallFieldTypeCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('sf:install-field-type')
            ->setDescription('Install a field type. Escape the backslash! Like so: This\\\Is\\\Namespace')
            ->setHelp('This command installs a field type, just give the namespace where to find the field.')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Field type namespace')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $input->getArgument('namespace');

        $fieldType = new FieldType();
        $type = explode('\\', $namespace);
        $type = $type[count($type) - 1];
        $fieldType->setType($type);
        $fieldType->setNamespace($namespace);

        $this->entityManager->persist($fieldType);
        $this->entityManager->flush();

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
                $fieldType->getCreated()->format(\DateTime::ATOM),
                $fieldType->getUpdated()->format(\DateTime::ATOM)
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
