<?php

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Service\SectionManager;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class UpdateSectionCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

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
            ->setName('sf:update-section')
            ->setDescription('Updates an existing section.')
            ->setHelp('This command allows you to update a section based on a yml section configuration. Pass along the path to a section configuration yml. Something like: section/blog.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->questionHelper = $this->getHelper('question');
        $this->showInstalledSections($input, $output);
    }

    private function showInstalledSections(InputInterface $input, OutputInterface $output): void
    {
        $sectionRepository = $this->entityManager->getRepository(Section::class);
        $sections = $sectionRepository->findAll();

        $this->renderTable($output, $sections);
        $this->updateWhatRecord($input, $output);
    }

    private function getSection(InputInterface $input, OutputInterface $output): Section
    {
        $sectionRepository = $this->entityManager->getRepository(Section::class);

        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output, $sectionRepository) {
            Assertion::integerish($id, 'Not an id (int), sorry.');
            $section = $sectionRepository->find($id);
            if (!$section) {
                throw new \Exception('No record with that id in database.');
            }
            return $section;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $section = $this->getSection($input, $output);
        $config = $input->getArgument('config');

        try {
            $sectionConfig = SectionConfig::create(
                Yaml::parse(
                    file_get_contents($config)
                )
            );
            $this->sectionManager->updateFromConfig($sectionConfig, $section);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }


        $output->writeln('<info>Section updated!</info>');
    }

    private function renderTable(OutputInterface $output, array $sections): void
    {
        $table = new Table($output);

        $rows = [];
        foreach ($sections as $section) {
            $rows[] = [
                $section->getId(),
                $section->getName(),
                $section->getHandle(),
                (string) $section->getConfig(),
                $section->getCreated()->format('Y-m-d'),
                $section->getUpdated()->format('Y-m-d')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed Sections</info>', array('colspan' => 6))
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}

