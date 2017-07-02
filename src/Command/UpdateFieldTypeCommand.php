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

class UpdateFieldTypeCommand extends Command
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
            ->setDescription('Creates a new section.')
            ->setHelp('This command allows you to create a section...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledFieldTypes($input, $output);
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
                $fieldType->getCreated()->format(\DateTime::ATOM),
                $fieldType->getUpdated()->format(\DateTime::ATOM)
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>The * column is what can be updated, type is updated automatically.</info>', array('colspan' => 5))
        ];

        $table
            ->setHeaders(['#id', 'type', '*namespace', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }

    private function showInstalledFieldTypes(InputInterface $input, OutputInterface $output)
    {
        $fieldTypes = $this->fieldTypeManager->readAll();

        $this->renderTable($output, $fieldTypes);
        $this->updateWhatRecord($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return FieldType|null
     */
    private function getFieldType(InputInterface $input, OutputInterface $output): FieldType
    {
        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->fieldTypeManager->read(Id::create($id));
            } catch (FieldTypeNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
            return null;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function getNamespace(InputInterface $input, OutputInterface $output, FieldType $fieldType): FullyQualifiedClassName
    {
        $updateQuestion = new Question('<question>Give a new namespace</question> (old: ' . $fieldType->getNamespace() . '): ');
        $updateQuestion->setValidator(function ($namespace) use ($output) {
            try {
                return FullyQualifiedClassName::create($namespace);
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

    private function updateRecord(InputInterface $input, OutputInterface $output, FieldType $fieldType, FullyQualifiedClassName $namespace)
    {
        $fieldType->setType($namespace->getClassName());
        $fieldType->setNamespace((string) $namespace);
        $fieldType = $this->fieldTypeManager->update($fieldType);
        $this->renderTable($output, [$fieldType]);

        $output->writeln('<info>Done!</info>');
    }

}
