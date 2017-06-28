<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\SectionManager;

class CreateSectionCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SectionManager
     */
    private $sectionManager;

    public function __construct(
        EntityManager $entityManager,
        SectionManager $sectionManager
    ) {
        $this->entityManager = $entityManager;
        $this->sectionManager = $sectionManager;

        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->setName('sf:create-section')
            ->setDescription('Creates a new section.')
            ->setHelp('This command allows you to create a section based on a yml section configuration. Pass along the path to a section configuration yml. Something like: section/blog.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $config = $input->getArgument('config');
        $sectionConfig = Yaml::parse(file_get_contents($config));

        try {
            $this->sectionManager->createFromConfig($sectionConfig);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
        }
    }
}
