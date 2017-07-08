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
use Tardigrades\Entity\Section;

abstract class ApplicationCommand extends Command
{
    protected function renderTable(OutputInterface $output, array $applications, string $info): void
    {
        $table = new Table($output);

        $rows = [];
        /** @var Application $application */
        foreach ($applications as $application) {

            $sections = $application->getSections();
            $sectionsText = '';
            /** @var Section $section */
            foreach ($sections as $section) {
                $sectionsText .= $section->getName() . "\n";
            }

            $languages = $application->getLanguages();
            $languageText = '';
            /** @var Language $language */
            foreach ($languages as $language) {
                $languageText .= (string) $language->getI18n() . "\n";
            }

            $rows[] = [
                $application->getId(),
                $application->getName(),
                $application->getHandle(),
                $sectionsText,
                $languageText,
                $application->getCreated()->format('D-m-y'),
                $application->getUpdated()->format('D-m-y')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>' . $info . '</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'sections', 'languages', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
