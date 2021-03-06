<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\Entity\FieldTypeInterface;

abstract class FieldTypeCommand extends Command
{
    protected function renderTable(OutputInterface $output, array $fieldTypes, string $info)
    {
        $table = new Table($output);

        $rows = [];
        /** @var FieldTypeInterface $fieldType */
        foreach ($fieldTypes as $fieldType) {
            $rows[] = [
                $fieldType->getId(),
                $fieldType->getType(),
                $fieldType->getFullyQualifiedClassName(),
                $fieldType->getCreated()->format('D-m-y'),
                $fieldType->getUpdated()->format('D-m-y'),
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>' . $info . '</info>', array('colspan' => 5))
        ];

        $table
            ->setHeaders(['#id', 'type', 'namespace', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
