<?php

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\FieldType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateFieldTypeCommand extends Command
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
            ->setName('sf:update-field-type')
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
        $fieldTypeRepository = $this->entityManager->getRepository(FieldType::class);
        $fieldTypes = $fieldTypeRepository->findAll();

        $this->renderTable($output, $fieldTypes);
        $this->updateWhatRecord($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return FieldType|null
     */
    private function getFieldType(InputInterface $input, OutputInterface $output)
    {
        $fieldTypeRepository = $this->entityManager->getRepository(FieldType::class);

        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output, $fieldTypeRepository) {
            Assertion::integerish($id, 'Not an id (int), sorry.');
            $fieldType = $fieldTypeRepository->find($id);
            if (!$fieldType) {
                throw new \Exception('No record with that id in database.');
            }
            return $fieldType;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function getNamespace(InputInterface $input, OutputInterface $output, FieldType $fieldType)
    {
        $updateQuestion = new Question('<question>Give a new namespace</question> (old: ' . $fieldType->getNamespace() . '): ');
        $updateQuestion->setValidator(function ($namespace) {
            Assertion::notEmpty($namespace, 'Oh come on, give me at least something.');

            return $namespace;
        });

        return $this->questionHelper->ask($input, $output, $updateQuestion);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output)
    {
        $fieldType = $this->getFieldType($input, $output);
        $namespace = $this->getNamespace($input, $output, $fieldType);

        $output->writeln('<info>Record with id #' . $fieldType->getId() . ' will be updated with namespace: </info>' . $namespace);

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing updated.</comment>');
            return;
        }

        $this->updateRecord($input, $output, $fieldType, $namespace);
    }

    private function updateRecord(InputInterface $input, OutputInterface $output, FieldType $fieldType, string $namespace)
    {
        $output->writeln('<info>Querying</info>');

        $type = explode('\\', $namespace);
        $type = $type[count($type) - 1];
        $fieldType->setType($type);
        $fieldType->setNamespace($namespace);

        $this->entityManager->flush();
        $this->renderTable($output, [$fieldType]);

        $output->writeln('<info>Done!</info>');
    }

}
