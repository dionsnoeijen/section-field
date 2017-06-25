<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Tardigrades\Entity\Field;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListFieldCommand extends Command
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
            ->setName('sf:list-field')
            ->setDescription('Show installed fields.')
            ->setHelp('This command lists all installed fields.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fieldTypeRepository = $this->entityManager->getRepository(Field::class);

        $fields = $fieldTypeRepository->findAll();

        $this->renderTable($output, $fields);
    }

    private function renderTable(OutputInterface $output, array $fields)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($fields as $field) {

            $config = '';
            foreach ($field->getConfig()['field'] as $key=>$value) {
                $config .= $key . ':' . $value . "\n";
            }

            $rows[] = [
                $field->getId(),
                $field->getName(),
                $field->getHandle(),
                $field->getFieldType()->getType(),
                $config,
                $field->getCreated()->format(\DateTime::ATOM),
                $field->getUpdated()->format(\DateTime::ATOM)
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed Fields</info>', array('colspan' => 6))
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'type', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
