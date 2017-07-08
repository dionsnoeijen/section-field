<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\Entity\Application;
use Tardigrades\Entity\Language;

abstract class LanguageCommand extends Command
{
    protected function renderTable(OutputInterface $output, array $languages, string $info): void
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
            new TableCell('<info>' . $info . '</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'i18n', 'application', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
