<?php

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\Section;

class DeleteSectionCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    private $questionHelper;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('sf:delete-section')
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

    private function getSection(InputInterface $input, OutputInterface $output)
    {
        $sectionRepository = $this->entityManager->getRepository(Section::class);

        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output, $sectionRepository) {
            Assertion::integerish($id, 'Not an id (int), sorry.');
            $section = $sectionRepository->find($id);
            if (!$section) {
                throw new \Exception('No record with that id id database.');
            }
            return $section;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output)
    {
        $section = $this->getSection($input, $output);

        $output->writeln('<info>Record with id #' . $section->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }

        $this->deleteRecord($input, $output, $section);
    }

    private function deleteRecord(InputInterface $input, OutputInterface $output, Section $section)
    {
        $this->entityManager->remove($section);
        $this->entityManager->flush();

        $output->writeln('<info>Removed!</info>');
    }

    private function renderTable(OutputInterface $output, array $sections)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($sections as $section) {
            $config = '';
            foreach ($section->getConfig()['section'] as $key=>$value) {
                $config .= $key . ':';
                if (is_array($value)) {
                    $config .= "\n";
                    foreach ($value as $subKey=>$subValue) {
                        $config .= " - {$subValue}\n";
                    }
                    continue;
                }
                $config .= $value . "\n";
            }

            $rows[] = [
                $section->getId(),
                $section->getName(),
                $section->getHandle(),
                $config,
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
