<?php

namespace Tardigrades\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;

class CreateSectionCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('sf:create-section')
            ->setDescription('Creates a new section.')
            ->setHelp('This command allows you to create a section based on a yml section configuration. Pass along the path to a section configuration yml. Something like: section/blog.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
            foreach ($sectionConfig['section']['fields'] as &$field) {
                $field = '\'' . $field . '\'';
            }

            $whereIn = implode(',', $sectionConfig['section']['fields']);

            $query = $this->entityManager->createQuery(
                "SELECT field FROM Tardigrades\Entity\Field field WHERE field.handle IN ({$whereIn})"
            );

            $fields = $query->getResult();
            $section = new Section();
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

            $output->writeln('<info>Section created!</info>');
            print_r($sectionConfig);

            return;
        }

        $output->writeln('<error>Invalid configuration file.</error>');
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
