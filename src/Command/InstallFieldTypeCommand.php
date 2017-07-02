<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

class InstallFieldTypeCommand extends Command
{
    /**
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    public function __construct(
        FieldTypeManager $fieldTypeManager
    ) {
        $this->fieldTypeManager = $fieldTypeManager;

        parent::__construct('sf:install-field-type');
    }

    protected function configure()
    {
        $this
            ->setDescription('Install a field type. Escape the backslash! Like so: This\\\Is\\\Namespace')
            ->setHelp('This command installs a field type, just give the namespace where to find the field.')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Field type namespace')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $input->getArgument('namespace');
        $fieldType = $this->fieldTypeManager->createWithFullyQualifiedClassName(
            FullyQualifiedClassName::create($namespace)
        );

        $this->renderTable($output, [$fieldType]);
    }

    private function renderTable(OutputInterface $output, array $fieldTypes)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($fieldTypes as $fieldType) {
            $rows[] = [
                $fieldType->getId(),
                $fieldType->getType(),
                $fieldType->getNamespace(),
                (string) $fieldType->getCreated(),
                (string) $fieldType->getUpdated()
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>FieldType installed</info>', array('colspan' => 5))
        ];

        $table
            ->setHeaders(['#id', 'type', 'namespace', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
