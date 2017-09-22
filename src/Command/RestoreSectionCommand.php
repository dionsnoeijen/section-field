<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\SectionEntityInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Service\SectionHistoryManagerInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

class RestoreSectionCommand extends SectionCommand
{
    /** @var SectionHistoryManagerInterface */
    private $sectionHistoryManager;

    public function __construct(
        SectionManagerInterface $sectionManager,
        SectionHistoryManagerInterface $sectionHistoryManager
    ) {
        $this->sectionHistoryManager = $sectionHistoryManager;

        parent::__construct($sectionManager, 'sf:restore-section');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Restore a section from history.')
            ->setHelp('Choose a section from history to move back to the active section position')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $sections = $this->sectionManager->readAll();
            $this->renderTable($output, $sections, 'All installed Sections');
            $this->restoreWhatRecord($input, $output);
        } catch (SectionNotFoundException $exception) {
            $output->writeln('No section found');
        }
    }

    private function restoreWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $section = $this->getSection($input, $output);

        $output->writeln('<info>Record with id #' . $section->getId() .
            ' will be restored, select a record from history to restore the section with.</info>');

        $this->renderTable($output, $section->getHistory()->toArray(), 'Section history');
        $sectionFromHistory = $this->getSectionFromHistory($input, $output);

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);
        if (!$this->getHelper('question')->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing restored.</comment>');
            return;
        }

        $this->sectionManager->restoreFromHistory($sectionFromHistory);

        $output->writeln('<info>Config Restored! Run the genereate-section command to finish rollback.</info>');
    }

    protected function getSectionFromHistory(InputInterface $input, OutputInterface $output): SectionInterface
    {
        $question = new Question('<question>Choose record.</question> (#id): ');

        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->sectionHistoryManager->read(Id::fromInt((int) $id));
            } catch (SectionNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
            return null;
        });

        return $this->getHelper('question')->ask($input, $output, $question);
    }
}
