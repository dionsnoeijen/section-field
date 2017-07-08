<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\EntityInterface\Application;
use Tardigrades\Entity\EntityInterface\Language;
use Tardigrades\SectionField\SectionFieldInterface\LanguageManager;
use Tardigrades\SectionField\Service\LanguageNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

class DeleteLanguageCommand extends Command
{
    /**
     * @var LanguageManager
     */
    private $languageManager;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    public function __construct(
        LanguageManager $languageManager
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

        $this->renderTable($output, $languages);
        $this->deleteWhatRecord($input, $output);
    }

    private function getLanguageRecord(InputInterface $input, OutputInterface $output): Language
    {
        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->languageManager->read(Id::create((int) $id));
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

        $output->writeln('<info>Removed!</info>');
    }

    private function renderTable(OutputInterface $output, array $languages): void
    {
        $table = new Table($output);

        $rows = [];
        /** @var Language $language */
        foreach ($languages as $language) {

            $applications = $language->getApplications();
            $applicationText = '';
            /** @var Application $application */
            foreach ($applications as $application) {
                $applicationText .= $application->getName() . "\n";
            }

            $rows[] = [
                $language->getId(),
                (string) $language->getI18n(),
                $applicationText,
                $language->getCreated()->format('D-m-y'),
                $language->getUpdated()->format('D-m-y')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed languages</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'i18n', 'application', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
