<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\LanguageManagerInterface;
use Tardigrades\SectionField\ValueObject\LanguageConfig;

class UpdateLanguageCommand extends LanguageCommand
{
    /** @var QuestionHelper */
    private $questionHelper;

    /** @var LanguageManagerInterface */
    private $languageManager;

    public function __construct(
        LanguageManagerInterface $languageManager
    ) {
        $this->languageManager = $languageManager;

        parent::__construct('sf:update-language');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Updates languages.')
            ->setHelp('With this command you can update installed languages based on a yml file, it will only add entries not yet in existence.')
            ->addArgument('config', InputArgument::REQUIRED, 'The language configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->questionHelper = $this->getHelper('question');

        try {
            $languageConfig = LanguageConfig::fromArray(
                Yaml::parse(
                    file_get_contents($input->getArgument('config'))
                )
            );
            $this->languageManager->updateByConfig($languageConfig);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }

        $this->renderTable($output, $this->languageManager->readAll(), 'Languages updated!');
    }
}

