<?php

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Field;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\Service\FieldManager;
use Tardigrades\SectionField\ValueObject\FieldConfig;

class UpdateFieldCommand extends Command
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

        parent::__construct('sf:update-field');
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates an existing field.')
            ->setHelp('Update field by giving a new or updated field config file.')
            ->addArgument('config', InputArgument::REQUIRED, 'The field configuration yml')
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

        $fields = $fieldRepository->findAll();

        $this->renderTable($output, $fields);
        $this->updateWhatRecord($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Field|null
     */
    private function getField(InputInterface $input, OutputInterface $output)
    {
        $fieldRepository = $this->entityManager->getRepository(Field::class);

        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output, $fieldRepository) {
            Assertion::integerish($id, 'Not an id (int), sorry.');
            $field = $fieldRepository->find($id);
            if (!$field) {
                throw new \Exception('No record with that id in database.');
            }
            return $field;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output)
    {
        $field = $this->getField($input, $output);
        $config = $input->getArgument('config');

        try {
            $fieldConfig = FieldConfig::create(
                Yaml::parse(
                    file_get_contents($config)
                )
            );
            $this->fieldManager->updateByConfig($fieldConfig, $field);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }

        $output->writeln('<error>Invalid field config.</error>');
    }

    private function renderTable(OutputInterface $output, array $fields)
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
