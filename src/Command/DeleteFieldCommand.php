<?php

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
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
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldManager;

class DeleteFieldCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(
        EntityManager $entityManager,
        FieldManager $fieldManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldManager = $fieldManager;

        parent::__construct('sf:delete-field');
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete field.')
            ->setHelp('Delete field.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledFields($input, $output);
    }

    private function showInstalledFields(InputInterface $input, OutputInterface $output)
    {
        $fieldRepository = $this->entityManager->getRepository(Field::class);
        $fieldTypes = $fieldRepository->findAll();

        $this->renderTable($output, $fieldTypes);
        $this->deleteWhatRecord($input, $output);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output)
    {
        $field = $this->getField($input, $output);

        $output->writeln('<info>Record with id #' . $field->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }

        $this->deleteRecord($input, $output, $field);
    }

    private function deleteRecord(InputInterface $input, OutputInterface $output, Field $field)
    {
        $this->fieldManager->delete($field);

        $output->writeln('<info>Removed!</info>');
    }

    private function getField(InputInterface $input, OutputInterface $output)
    {
        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                Assertion::integerish($id, 'Not an id (int), sorry.');
                $field = $this->fieldManager->read($id);
                return $field;
            } catch (\Exception $exception) {
                $output->writeln("<error>{$exception->getMessage()}</error>");
            }
            return;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function renderTable(OutputInterface $output, array $fields)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($fields as $field) {

            $config = '';
            foreach ($field->getConfig()['field'] as $key=>$value) {
                $config .= $key . ':' . $value . "\n";
            }

            $rows[] = [
                $field->getId(),
                $field->getName(),
                $field->getHandle(),
                $field->getFieldType()->getType(),
                $config,
                $field->getCreated()->format(\DateTime::ATOM),
                $field->getUpdated()->format(\DateTime::ATOM)
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
