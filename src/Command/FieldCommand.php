<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldTranslation;

abstract class FieldCommand extends Command
{
    protected function renderTable(OutputInterface $output, array $fields, string $info): void
    {
        $table = new Table($output);

        $rows = [];
        /** @var Field $field */
        foreach ($fields as $field) {

            $translations = $field->getFieldTranslations();
            /** @var FieldTranslation $translation */
            $names = '';
            foreach ($translations as $translation) {
                $names .=
                    $translation->getLanguage()->getI18n() . ' ' .
                    $translation->getName() . "\n";
            }

            $rows[] = [
                $field->getId(),
                $names,
                $field->getHandle(),
                $field->getFieldType()->getType(),
                (string) $field->getConfig(),
                $field->getCreated()->format('D-m-y'),
                $field->getUpdated()->format('D-m-y')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>' . $info . '</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'type', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
