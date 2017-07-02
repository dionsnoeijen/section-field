<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class ListSectionCommand extends Command
{
    /**
     * @var SectionManager
     */
    private $sectionManager;

    public function __construct(
        SectionManager $sectionManager
    ) {
        $this->sectionManager = $sectionManager;

        parent::__construct('sf:list-section');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show installed sections.')
            ->setHelp('This command lists all installed sections.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $sections = $this->sectionManager->readAll();

        $this->renderTable($output, $sections);
    }

    private function renderTable(OutputInterface $output, array $sections): void
    {
        $table = new Table($output);

        $rows = [];
        foreach ($sections as $section) {
            $rows[] = [
                $section->getId(),
                $section->getName(),
                $section->getHandle(),
                (string) $section->getConfig(),
                (string) $section->getCreated(),
                (string) $section->getUpdated()
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed Sections</info>', ['colspan' => 6])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}

