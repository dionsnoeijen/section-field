<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\Entity\EntityInterface\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;

class ListFieldTypeCommand extends Command
{
    /**
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    public function __construct(
        FieldTypeManager $fieldTypeManager
    ) {
        $this->fieldTypeManager = $fieldTypeManager;

        parent::__construct('sf:list-field-type');
    }

    protected function configure()
    {
        $this
            ->setDescription('Show installed field types.')
            ->setHelp('This command lists all installed field types.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fieldTypes = $this->fieldTypeManager->readAll();

        $this->renderTable($output, $fieldTypes);
    }

    private function renderTable(OutputInterface $output, array $fieldTypes)
    {
        $table = new Table($output);

        $rows = [];
        /** @var FieldType $fieldType */
        foreach ($fieldTypes as $fieldType) {
            $rows[] = [
                $fieldType->getId(),
                $fieldType->getType(),
                $fieldType->getFullyQualifiedClassName(),
                $fieldType->getCreated()->format('D-m-y'),
                $fieldType->getUpdated()->format('D-m-y')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed FieldTypes</info>', ['colspan' => 5])
        ];

        $table
            ->setHeaders(['#id', 'type', 'namespace', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
