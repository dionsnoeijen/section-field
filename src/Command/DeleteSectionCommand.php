<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Service\SectionManager;
use Tardigrades\SectionField\Service\SectionNotFoundException;

class DeleteSectionCommand extends Command
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

        parent::__construct('sf:delete-section');
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete section.')
            ->setHelp('Delete section.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledSections($input, $output);
    }

    private function showInstalledSections(InputInterface $input, OutputInterface $output)
    {
        $sectionRepository = $this->entityManager->getRepository(Section::class);

        $sections = $sectionRepository->findAll();

        $this->renderTable($output, $sections);
        $this->deleteWhatRecord($input, $output);
    }

    private function getSection(InputInterface $input, OutputInterface $output): Section
    {
        $question = new Question('<question>What record do you want to delete?</question> (#id): ');

        $question->setValidator(function ($id) use ($output) {
            Assertion::integerish($id, 'Not an id (int), sorry.');
            try {
                $section = $this->sectionManager->read($id);
            } catch (SectionNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
                return null;
            }

            return $section;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $section = $this->getSection($input, $output);

        $output->writeln('<info>Record with id #' . $section->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }

        $this->sectionManager->delete($section);

        $output->writeln('<info>Removed!</info>');
    }

    private function renderTable(OutputInterface $output, array $sections)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($sections as $section) {
            $rows[] = [
                $section->getId(),
                $section->getName(),
                $section->getHandle(),
                (string) $section->getConfig(),
                $section->getCreated()->format(\DateTime::ATOM),
                $section->getUpdated()->format(\DateTime::ATOM)
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
