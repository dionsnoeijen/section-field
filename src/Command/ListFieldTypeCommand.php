<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Tardigrades\Entity\FieldType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListFieldTypeCommand extends Command
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
            ->setName('sf:list-field-type')
            ->setDescription('Show installed field types.')
            ->setHelp('This command lists all installed field types.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fieldTypeRepository = $this->entityManager->getRepository(FieldType::class);

        $fieldTypes = $fieldTypeRepository->findAll();

        $this->renderTable($output, $fieldTypes);
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
            new TableCell('<info>All installed FieldTypes</info>', array('colspan' => 5))
        ];

        $table
            ->setHeaders(['#id', 'type', 'namespace', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
