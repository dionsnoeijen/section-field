<?php

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\Entity\EntityInterface\FieldTranslation;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;

class ListFieldCommand extends Command
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(
        FieldManager $fieldManager
    ) {
        $this->fieldManager = $fieldManager;

        parent::__construct('sf:list-field');
    }

    protected function configure()
    {
        $this
            ->setDescription('Show installed fields.')
            ->setHelp('This command lists all installed fields.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fields = $this->fieldManager->readAll();

        $this->renderTable($output, $fields);
    }

    private function renderTable(OutputInterface $output, array $fields)
    {
        $table = new Table($output);

        $rows = [];
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
            new TableCell('<info>All installed Fields</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'type', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
