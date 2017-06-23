<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class CreateSectionCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sf:create-section')
            ->setDescription('Creates a new section.')
            ->setHelp('This command allows you to create a section based on a yml section configuration. Pass along the path to a section configuration yml. Something like: section-config/blog.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Section Creator',
            '============',
            '',
        ]);

        $config = $input->getArgument('config');
        $parsed = Yaml::parse(file_get_contents($config));

        print_r($parsed);
    }
}
