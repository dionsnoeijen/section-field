<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
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
            ->setDescription('Creates a new section.')
            ->setHelp('This command allows you to create a section...')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Field type namespace')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'FieldType Installer',
            '============',
            '',
        ]);
        $namespace = $input->getArgument('namespace');

        $fieldType = new FieldType();


//        $product = new Product();
//        $product->setName($newProductName);
//
//        $entityManager->persist($product);
//        $entityManager->flush();
    }
}
