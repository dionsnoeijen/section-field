<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteFieldTypeCommand extends Command
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
            ->setName('sf:delete-field-type')
            ->setDescription('Delete field type.')
            ->setHelp('Delete field type')
            ->addArgument('config', InputArgument::REQUIRED, 'The field configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'FieldType Installer',
            '============',
            ''
        ]);


    }
}
