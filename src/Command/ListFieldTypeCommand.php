<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;

class ListFieldTypeCommand extends FieldTypeCommand
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

        $this->renderTable($output, $fieldTypes, 'All installed FieldTypes');
    }
}
