<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\EntityInterface\FieldTranslation;
use Tardigrades\Entity\Field;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\Service\FieldNotFoundException;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Id;

class UpdateFieldCommand extends Command
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

    private function showInstalledFields(InputInterface $input, OutputInterface $output): void
    {
        $fields = $this->fieldManager->readAll();

        $this->renderTable($output, $fields);
        $this->updateWhatRecord($input, $output);
    }

    private function getField(InputInterface $input, OutputInterface $output): Field
    {
        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->fieldManager->read(Id::create((int) $id));
            } catch (FieldNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
            return null;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output): void
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

        $output->writeln('<info>Field updated!</info>');
    }

    private function renderTable(OutputInterface $output, array $fields): void
    {
        $table = new Table($output);

        $rows = [];
        foreach ($fields as $field) {
            $translations = $field->getFieldTranslations();
            /** @var FieldTranslation $translation */
            $names = '';
            foreach ($translations as $translation) {
                $names .=
                    $translation->getLanguage()->getI18n() . ' ' .
                    $translation->getName() . "\n";
            }

            $rows[] = [
                $field->getId(),
                $names,
                $field->getHandle(),
                $field->getFieldType()->getType(),
                (string) $field->getConfig(),
                $field->getCreated()->format('D-m-y'),
                $field->getUpdated()->format('D-m-y')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed Fields</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'type', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}
