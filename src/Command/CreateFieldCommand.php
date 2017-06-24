<?php

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class CreateFieldCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sf:create-field')
            ->setDescription('Creates a field.')
            ->setHelp('Create field based on a config file')
            ->addArgument('config', InputArgument::REQUIRED, 'The field configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'FieldType Installer',
            '============',
            ''
        ]);

        $config = $input->getArgument('config');
        $parsed = Yaml::parse(file_get_contents($config));

        if (key($parsed) === 'field') {
            print_r($parsed);
        }
    }
}
