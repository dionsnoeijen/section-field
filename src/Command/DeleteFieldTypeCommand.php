<?php

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldTypeManager;

class DeleteFieldTypeCommand extends Command
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
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    public function __construct(
        EntityManager $entityManager,
        FieldTypeManager $fieldTypeManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldTypeManager = $fieldTypeManager;

        parent::__construct('sf:delete-field-type');
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete field type.')
            ->setHelp('Delete field type.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledFieldTypes($input, $output);
    }

    private function showInstalledFieldTypes(InputInterface $input, OutputInterface $output)
    {
        $fieldTypeRepository = $this->entityManager->getRepository(FieldType::class);
        $fieldTypes = $fieldTypeRepository->findAll();

        $this->renderTable($output, $fieldTypes);
        $this->deleteWhatRecord($input, $output);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output)
    {
        $fieldType = $this->getFieldType($input, $output);

        $output->writeln('<info>Record with id #' . $fieldType->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }

        $this->deleteRecord($input, $output, $fieldType);
    }

    private function deleteRecord(InputInterface $input, OutputInterface $output, FieldType $fieldType)
    {
        $this->fieldTypeManager->delete($fieldType);

        $output->writeln('<info>Removed!</info>');
    }

    private function getFieldType(InputInterface $input, OutputInterface $output)
    {
        $fieldTypeRepository = $this->entityManager->getRepository(FieldType::class);

        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output, $fieldTypeRepository) {
            Assertion::integerish($id, 'Not an id (int), sorry.');
            $fieldType = $fieldTypeRepository->find($id);
            if (!$fieldType) {
                throw new \Exception('No record with that id id database.');
            }
            return $fieldType;
        });

        return $this->questionHelper->ask($input, $output, $question);
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

        $table
            ->setHeaders(['#id', 'type', 'namespace', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
