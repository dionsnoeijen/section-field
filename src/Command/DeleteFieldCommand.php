<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Assert\Assertion;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\Field;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\Service\FieldNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

class DeleteFieldCommand extends Command
{
    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(
        FieldManager $fieldManager
    ) {
        $this->fieldManager = $fieldManager;

        parent::__construct('sf:delete-field');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Delete field.')
            ->setHelp('Shows a list of installed fields, choose the field you would like to delete.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledFields($input, $output);
    }

    private function showInstalledFields(InputInterface $input, OutputInterface $output): void
    {
        $fieldTypes = $this->fieldManager->readAll();

        $this->renderTable($output, $fieldTypes);
        $this->deleteWhatRecord($input, $output);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $field = $this->getField($input, $output);

        $output->writeln('<info>Record with id #' . $field->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }

        $this->fieldManager->delete($field);

        $output->writeln('<info>Removed!</info>');
    }

    private function getField(InputInterface $input, OutputInterface $output): Field
    {
        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->fieldManager->read(Id::create($id));
            } catch (FieldNotFoundException $exception) {
                $output->writeln("<error>{$exception->getMessage()}</error>");
            }
            return null;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function renderTable(OutputInterface $output, array $fields): void
    {
        $table = new Table($output);

        $rows = [];
        foreach ($fields as $field) {
            $rows[] = [
                $field->getId(),
                $field->getName(),
                $field->getHandle(),
                $field->getFieldType()->getType(),
                (string) $field->getConfig(),
                (string) $field->getCreated(),
                (string) $field->getUpdated()
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed Fields</info>', array('colspan' => 6))
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'type', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
