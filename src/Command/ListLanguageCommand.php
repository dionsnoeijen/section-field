<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\Entity\EntityInterface\Application;
use Tardigrades\Entity\EntityInterface\Language;
use Tardigrades\SectionField\SectionFieldInterface\LanguageManager;

class ListLanguageCommand extends Command
{
    private $languageManager;

    public function __construct(
        LanguageManager $languageManager
    ) {
        $this->languageManager = $languageManager;

        parent::__construct('sf:list-language');
    }

    protected function configure()
    {
        $this
            ->setDescription('List language')
            ->setHelp('List all installed languages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $languages = $this->languageManager->readAll();

        $this->renderTable($output, $languages);
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
                $applicationText .= (string) $application->getName() . "\n";
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
            ->setHeaders(['#id', 'name', 'handle', 'sections', 'languages', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
