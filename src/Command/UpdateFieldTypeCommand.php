<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\FieldType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;
use Tardigrades\SectionField\Service\FieldTypeNotFoundException;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;

class UpdateFieldTypeCommand extends FieldTypeCommand
{
    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    public function __construct(
        FieldTypeManager $fieldTypeManager
    ) {
        $this->fieldTypeManager = $fieldTypeManager;

        parent::__construct('sf:update-field-type');
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates a field type.')
            ->setHelp('Update a field type based on new fully qualified class name.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledFieldTypes($input, $output);
    }

    private function showInstalledFieldTypes(InputInterface $input, OutputInterface $output)
    {
        $fieldTypes = $this->fieldTypeManager->readAll();

        $this->renderTable($output, $fieldTypes, 'The * column is what can be updated, type is updated automatically.');
        $this->updateWhatRecord($input, $output);
    }

    private function getFieldType(InputInterface $input, OutputInterface $output): FieldType
    {
        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->fieldTypeManager->read(Id::fromInt((int) $id));
            } catch (FieldTypeNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
            return null;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function getNamespace(InputInterface $input, OutputInterface $output, FieldType $fieldType): FullyQualifiedClassName
    {
        $updateQuestion = new Question('<question>Give a new fully qualified class name</question> (old: ' . $fieldType->getFullyQualifiedClassName() . '): ');
        $updateQuestion->setValidator(function ($fullyQualifiedClassName) use ($output) {
            try {
                return FullyQualifiedClassName::fromString($fullyQualifiedClassName);
            } catch (\Exception $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
        });

        return $this->questionHelper->ask($input, $output, $updateQuestion);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output)
    {
        $fieldType = $this->getFieldType($input, $output);
        $namespace = $this->getNamespace($input, $output, $fieldType);

        $output->writeln('<info>Record with id #' . $fieldType->getId() . ' will be updated with namespace: </info>' . (string) $namespace);

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing updated.</comment>');
            return;
        }

        $this->updateRecord($input, $output, $fieldType, $namespace);
    }

    private function updateRecord(InputInterface $input, OutputInterface $output, FieldType $fieldType, FullyQualifiedClassName $fullyQualifiedClassName)
    {
        $fieldType->setType($fullyQualifiedClassName->getClassName());
        $fieldType->setFullyQualifiedClassName((string) $fullyQualifiedClassName);
        $this->fieldTypeManager->update();
        $this->renderTable($output, [$fieldType], 'The * column is what can be updated, type is updated automatically.');

        $output->writeln('<info>FieldType Updated!</info>');
    }

}
