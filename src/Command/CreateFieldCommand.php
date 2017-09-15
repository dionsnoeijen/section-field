<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

class CreateFieldCommand extends FieldCommand
{
    /** @var FieldManagerInterface */
    private $fieldManager;

    public function __construct(
        FieldManagerInterface $fieldManager
    ) {
        $this->fieldManager = $fieldManager;

        parent::__construct('sf:create-field');
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a field.')
            ->setHelp('Create field based on a config file.')
            ->addArgument('config', InputArgument::REQUIRED, 'The field configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getArgument('config');

        try {
            if (file_exists($config)) {
                $parsed = Yaml::parse(file_get_contents($config));
                if (is_array($parsed)) {
                    $fieldConfig = FieldConfig::fromArray($parsed);
                    $this->fieldManager->createByConfig($fieldConfig);
                    $output->writeln('<info>Field created!</info>');
                    return;
                }
            }
            throw new Exception('No valid config found.');
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid field config. {$exception->getMessage()}</error>");
        }
    }
}
