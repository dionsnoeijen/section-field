<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldManager;
use Tardigrades\SectionField\ValueObject\FieldConfig;

class CreateFieldCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(
        EntityManager $entityManager,
        FieldManager $fieldManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldManager = $fieldManager;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('sf:create-field')
            ->setDescription('Creates a field.')
            ->setHelp('Create field based on a config file.')
            ->addArgument('config', InputArgument::REQUIRED, 'The field configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getArgument('config');

        try {
            $fieldConfig = FieldConfig::create(
                Yaml::parse(file_get_contents($config))
            );
            $this->fieldManager->createByConfig($fieldConfig);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid field config. {$exception->getMessage()}</error>");
        }
    }
}
