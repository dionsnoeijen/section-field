<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\LanguageInterface;
use Tardigrades\SectionField\Service\LanguageManagerInterface;
use Tardigrades\SectionField\Service\LanguageNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

class DeleteLanguageCommand extends LanguageCommand
{
    /** @var LanguageManagerInterface */
    private $languageManager;

    /** @var QuestionHelper */
    private $questionHelper;

    public function __construct(
        LanguageManagerInterface $languageManager
    ) {
        $this->languageManager = $languageManager;

        parent::__construct('sf:delete-language');
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete language')
            ->setHelp('Shows a list of installed languages, choose the language you would like to delete.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledLanguages($input, $output);
    }

    private function showInstalledLanguages(InputInterface $input, OutputInterface $output): void
    {
        $languages = $this->languageManager->readAll();

        $this->renderTable($output, $languages, 'All installed languages');
        $this->deleteWhatRecord($input, $output);
    }

    private function getLanguageRecord(InputInterface $input, OutputInterface $output): LanguageInterface
    {
        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->languageManager->read(Id::fromInt((int) $id));
            } catch (LanguageNotFoundException $exception) {
                $output->writeln("<error>{$exception->getMessage()}</error>");
            }
            return null;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $language = $this->getLanguageRecord($input, $output);

        $output->writeln('<info>Record with id #' . $language->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }
        $this->languageManager->delete($language);

        try {
            $this->renderTable($output, $this->languageManager->readAll(), 'Removed!');
        } catch (LanguageNotFoundException $exception) {
            $output->writeln('No languages left');
        }
    }
}
