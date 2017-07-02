<?php
declare (strict_types=1);

namespace Tardigrades\Command;

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
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class UpdateSectionCommand extends Command
{
    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var SectionManager
     */
    private $sectionManager;

    public function __construct(
        SectionManager $sectionManager
    ) {
        $this->sectionManager = $sectionManager;

        parent::__construct('sf:update-section');
    }

    protected function configure(): void
    {
        $this
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
        $sections = $this->sectionManager->readAll();

        $this->renderTable($output, $sections);
        $this->updateWhatRecord($input, $output);
    }

    private function getSection(InputInterface $input, OutputInterface $output): Section
    {
        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->sectionManager->read(Id::create($id));
            } catch (SectionNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
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
                (string) $section->getCreated(),
                (string) $section->getUpdated()
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed Sections</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}

