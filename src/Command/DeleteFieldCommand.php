<?php

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldType;

class DeleteFieldCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    private $questionHelper;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('sf:delete-field')
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
        $this->entityManager->remove($field);
        $this->entityManager->flush();

        $output->writeln('<info>Removed!</info>');
    }

    private function getField(InputInterface $input, OutputInterface $output)
    {
        $fieldRepository = $this->entityManager->getRepository(Field::class);

        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output, $fieldRepository) {
            Assertion::integerish($id, 'Not an id (int), sorry.');
            $field = $fieldRepository->find($id);
            if (!$field) {
                throw new \Exception('No record with that id id database.');
            }
            return $field;
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
