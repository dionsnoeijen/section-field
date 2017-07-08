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
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\EntityInterface\Application;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\SectionFieldInterface\LanguageManager;
use Tardigrades\SectionField\ValueObject\LanguageConfig;

class UpdateLanguageCommand extends Command
{
    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var LanguageManager
     */
    private $languageManager;

    public function __construct(
        LanguageManager $languageManager
    ) {
        $this->languageManager = $languageManager;

        parent::__construct('sf:update-language');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Updates languages.')
            ->setHelp('With this command you can update installed languages based on a yml file, it will only add entries not yet in existence.')
            ->addArgument('config', InputArgument::REQUIRED, 'The language configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->questionHelper = $this->getHelper('question');

        try {
            $languageConfig = LanguageConfig::create(
                Yaml::parse(
                    file_get_contents($input->getArgument('config'))
                )
            );
            $this->languageManager->updateByConfig($languageConfig);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }

        $this->renderTable($output, $this->languageManager->readAll());
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
            new TableCell('<info>Languages updated!</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'i18n', 'application', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}

