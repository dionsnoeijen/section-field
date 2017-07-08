<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Assert\Assertion;
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
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

class DeleteSectionCommand extends SectionCommand
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
        $sections = $this->sectionManager->readAll();

        $this->renderTable($output, $sections, 'All installed Sections');
        $this->deleteWhatRecord($input, $output);
    }

    private function getSection(InputInterface $input, OutputInterface $output): Section
    {
        $question = new Question('<question>What record do you want to delete?</question> (#id): ');

        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->sectionManager->read(Id::create((int) $id));
            } catch (SectionNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
            return null;
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
}
