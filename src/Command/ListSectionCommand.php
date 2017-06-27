<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\Entity\Section;

class ListSectionCommand extends Command
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
            ->setName('sf:list-section')
            ->setDescription('Show installed sections.')
            ->setHelp('This command lists all installed sections.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');
        $this->showInstalledSections($input, $output);
    }

    private function showInstalledSections(InputInterface $input, OutputInterface $output)
    {
        $sectionRepository = $this->entityManager->getRepository(Section::class);

        $sections = $sectionRepository->findAll();

        $this->renderTable($output, $sections);
    }

    private function renderTable(OutputInterface $output, array $sections)
    {
        $table = new Table($output);

        $rows = [];
        foreach ($sections as $section) {
            $config = '';
            foreach ($section->getConfig()['section'] as $key=>$value) {
                $config .= $key . ':';
                if (is_array($value)) {
                    $config .= "\n";
                    foreach ($value as $subKey=>$subValue) {
                        $config .= " - {$subValue}\n";
                    }
                    continue;
                }
                $config .= $value . "\n";
            }

            $rows[] = [
                $section->getId(),
                $section->getName(),
                $section->getHandle(),
                $config,
                $section->getCreated()->format(\DateTime::ATOM),
                $section->getUpdated()->format(\DateTime::ATOM)
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>All installed Sections</info>', array('colspan' => 6))
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'config', 'created', 'updated'])
            ->setRows($rows)
        ;
        $table->render();
    }
}

