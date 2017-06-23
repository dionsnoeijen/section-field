<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallFieldTypeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sf:install-field-type')
            ->setDescription('Creates a new section.')
            ->setHelp('This command allows you to create a section...')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'FieldType Installer',
            '============',
            '',
        ]);

        $config = $input->getArgument('config');

        $output->writeln($config);
    }
}
