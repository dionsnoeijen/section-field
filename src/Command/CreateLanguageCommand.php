<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\SectionFieldInterface\LanguageManager;
use Tardigrades\SectionField\ValueObject\LanguageConfig;

class CreateLanguageCommand extends LanguageCommand
{
    /** @var LanguageManager */
    private $languageManager;

    public function __construct(
        LanguageManager $languageManager
    ) {
        $this->languageManager = $languageManager;

        parent::__construct('sf:create-language');
    }

    protected function configure()
    {
        $this
            ->setDescription('Create language')
            ->setHelp('Create language')
            ->addArgument('config', InputArgument::REQUIRED, 'The language configuration yml');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getArgument('config');

        try {
            $languageConfig = LanguageConfig::fromArray(
                Yaml::parse(file_get_contents($config))
            );
            $this->languageManager->createByConfig($languageConfig);
            $output->writeln('<info>Languages created!</info>');
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid config. {$exception->getMessage()}</error>");
        }
    }
}
