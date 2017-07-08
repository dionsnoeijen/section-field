<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;use Tardigrades\SectionField\Service\FieldTypeNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

class DeleteFieldTypeCommand extends FieldTypeCommand
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

        parent::__construct('sf:delete-field-type');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Delete field type.')
            ->setHelp('Delete field type.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->questionHelper = $this->getHelper('question');

        $this->showInstalledFieldTypes($input, $output);
    }

    private function showInstalledFieldTypes(InputInterface $input, OutputInterface $output): void
    {
        $fieldTypes = $this->fieldTypeManager->readAll();

        $this->renderTable($output, $fieldTypes, 'Installed field types');
        $this->deleteWhatRecord($input, $output);
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $fieldType = $this->getFieldType($input, $output);

        $output->writeln('<info>Record with id #' . $fieldType->getId() . ' will be deleted</info>');

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
            return;
        }

        $this->fieldTypeManager->delete($fieldType);

        $output->writeln('<info>Removed!</info>');
    }

    private function getFieldType(InputInterface $input, OutputInterface $output): FieldType
    {
        $question = new Question('<question>What record do you want to delete?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->fieldTypeManager->read(Id::create((int) $id));
            } catch (FieldTypeNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
            return null;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }
}
