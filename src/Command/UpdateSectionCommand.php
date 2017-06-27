<?php

namespace Tardigrades\Command;

use Assert\Assertion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;

class UpdateSectionCommand extends Command
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
            ->setName('sf:update-section')
            ->setDescription('Updates an existing section.')
            ->setHelp('This command allows you to update a section based on a yml section configuration. Pass along the path to a section configuration yml. Something like: section/blog.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
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
        $this->updateWhatRecord($input, $output);
    }

    private function getSection(InputInterface $input, OutputInterface $output)
    {
        $sectionRepository = $this->entityManager->getRepository(Section::class);

        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output, $sectionRepository) {
            Assertion::integerish($id, 'Not an id (int), sorry.');
            $section = $sectionRepository->find($id);
            if (!$section) {
                throw new \Exception('No record with that id in database.');
            }
            return $section;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output)
    {
        $section = $this->getSection($input, $output);

        $config = $input->getArgument('config');
        $sectionConfig = Yaml::parse(file_get_contents($config));

        if (isset($sectionConfig) &&
            key($sectionConfig) === 'section' &&
            !empty($sectionConfig['section']['name']) &&
            !empty($sectionConfig['section']['fields']) &&
            is_array($sectionConfig['section']['fields']) &&
            !empty($sectionConfig['section']['slug']) &&
            !empty($sectionConfig['section']['default'])
        ) {
            $handle = $this->camelCase($sectionConfig['section']['name']);
            $sectionRepository = $this->entityManager->getRepository(Section::class);

            if (isset($section)) {
                // Unlink the currently assigned links
                $currentFields = $section->getFields();
                foreach ($currentFields as $currentField) {
                    $section->removeField($currentField);
                }

                // Find fields to assign
                $fieldsConfig = [];
                foreach ($sectionConfig['section']['fields'] as $fieldConfig) {
                    $fieldsConfig[] = '\'' . $fieldConfig . '\'';
                }
                $whereIn = implode(',', $fieldsConfig);
                $query = $this->entityManager->createQuery(
                    "SELECT field FROM Tardigrades\Entity\Field field WHERE field.handle IN ({$whereIn})"
                );
                $fields = $query->getResult();

                $section->setName($sectionConfig['section']['name']);
                $section->setHandle($this->camelCase($sectionConfig['section']['name']));
                foreach ($fields as $field) {
                    $section->addField($field);
                }
                $section->setConfig((object) $sectionConfig);

                try {
                    $this->entityManager->persist($section);
                    $this->entityManager->flush();
                } catch (\Exception $exception) {
                    $output->writeln('<error>Error: Probably duplication error, the handle must be unique!</error>');
                    return;
                }

                $output->writeln('<info>Section updated!</info>');
                return;
            }
            return;
        }

        $output->writeln('<error>Invalid configuration file.</error>');
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

    private function camelCase($str, array $noStrip = [])
    {
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }
}

