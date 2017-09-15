<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\ApplicationInterface;
use Tardigrades\SectionField\Service\ApplicationManagerInterface;
use Tardigrades\SectionField\Service\ApplicationNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

class DeleteApplicationCommand extends ApplicationCommand
{
    /**
     * @var ApplicationManagerInterface
     */
    private $applicationManager;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    public function __construct(
        ApplicationManagerInterface $applicationManager
    ) {
        $this->applicationManager = $applicationManager;

        parent::__construct('sf:delete-application');
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete application')
            ->setHelp('Shows a list of installed applications, choose the application you would like to delete.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledApplications($input, $output);
    }

    private function showInstalledApplications(InputInterface $input, OutputInterface $output): void
    {
        $appliactions = $this->applicationManager->readAll();

        $this->renderTable($output, $appliactions, 'All installed Applications');
        $this->deleteWhatRecord($input, $output);
    }

    private function getApplicationRecord(InputInterface $input, OutputInterface $output): ApplicationInterface
    {
        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->applicationManager->read(Id::fromInt((int) $id));
            } catch (ApplicationNotFoundException $exception) {
                $output->writeln("<error>{$exception->getMessage()}</error>");
            }
            return null;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $application = $this->getApplicationRecord($input, $output);

        $output->writeln('<info>Record with id #' . $application->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }
        $this->applicationManager->delete($application);

        $output->writeln('<info>Removed!</info>');
    }
}
