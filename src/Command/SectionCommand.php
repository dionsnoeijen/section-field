<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

abstract class SectionCommand extends Command
{
    /** @var SectionManager */
    protected $sectionManager;

    public function __construct(
        SectionManager $sectionManager,
        string $name
    ) {
        $this->sectionManager = $sectionManager;

        parent::__construct($name);
    }

    protected function renderTable(OutputInterface $output, array $sections, string $info)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($sections as $section) {
            $rows[] = [
                $section->getId(),
                $section->getName(),
                $section->getHandle(),
                (string) $section->getConfig(),
                $section->getUpdated()->format('D-m-y')
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>' . $info . '</info>', ['colspan' => 5])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'config', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }

    protected function getSection(InputInterface $input, OutputInterface $output): Section
    {
        $question = new Question('<question>Choose record.</question> (#id): ');

        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->sectionManager->read(Id::fromInt((int) $id));
            } catch (SectionNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
            return null;
        });

        return $this->getHelper('question')->ask($input, $output, $question);
    }
}
