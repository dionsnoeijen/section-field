<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class DeleteSectionCommand extends SectionCommand
{
    public function __construct(
        SectionManager $sectionManager
    ) {
        parent::__construct($sectionManager, 'sf:delete-section');
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
        $sections = $this->sectionManager->readAll();
        $this->renderTable($output, $sections, 'All installed Sections');
        $this->deleteWhatRecord($input, $output);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $section = $this->getSection($input, $output);

        $output->writeln('<info>Record with id #' . $section->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->getHelper('question')->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }

        $this->sectionManager->delete($section);

        $output->writeln('<info>Removed!</info>');
    }
}
