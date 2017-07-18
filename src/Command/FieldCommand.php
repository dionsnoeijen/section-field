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
    protected function renderTable(
        OutputInterface $output,
        array $fields,
        string $info
    ): void {
        $table = new Table($output);

        $rows = [];
        /** @var Field $field */
        foreach ($fields as $field) {

            $translations = $field->getFieldTranslations();
            /** @var FieldTranslation $translation */
            $names = '';
            $labels = '';
            foreach ($translations as $translation) {
                $names .=
                    $translation->getLanguage()->getI18n() . ' ' .
                    $translation->getName() . "\n";

                $labels .=
                    $translation->getLanguage()->getI18n() . ' ' .
                    $translation->getLabel() . "\n";
            }

            $rows[] = [
                $field->getId(),
                $names,
                $labels,
                $field->getHandle(),
                $field->getFieldType()->getType(),
                (string) $field->getConfig(),
                $field->getUpdated()->format('d-m-y h:i')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>' . $info . '</info>', ['colspan' => 7])
        ];

        $table
            ->setHeaders([
                '#id', 'name', 'label',
                'handle', 'type', 'config',
                'updated'
            ])
            ->setRows($rows)
        ;
        $table->render();
    }
}
