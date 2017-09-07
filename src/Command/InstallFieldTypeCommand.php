<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

class InstallFieldTypeCommand extends FieldTypeCommand
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
            ->setDescription('Install a field type. Escape the backslash! Like so: This\\\Is\\\ClassName')
            ->setHelp('This command installs a field type, just give the namespace where to find the field.')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Field type namespace')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $input->getArgument('namespace');
        $fieldType = $this->fieldTypeManager->createWithFullyQualifiedClassName(
            FullyQualifiedClassName::fromString($namespace)
        );

        $this->renderTable($output, [$fieldType], 'FieldType installed!');
    }
}
