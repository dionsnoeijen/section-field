<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\EntityInterface\Application;
use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\Id;

class UpdateApplicationCommand extends ApplicationCommand
{
    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var ApplicationManager
     */
    private $applicationManager;

    public function __construct(
        ApplicationManager $applicationManager
    ) {
        $this->applicationManager = $applicationManager;

        parent::__construct('sf:update-application');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Updates an existing application.')
            ->setHelp('This command allows you to update an application based on a yml application configuration. Pass along the path to a application configuration yml. Something like: application/application.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The application configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->questionHelper = $this->getHelper('question');
        $this->showInstalledApplications($input, $output);
    }

    private function showInstalledApplications(InputInterface $input, OutputInterface $output): void
    {
        $applications = $this->applicationManager->readAll();

        $this->renderTable($output, $applications, 'All installed Applications');
        $this->updateWhatRecord($input, $output);
    }

    private function getApplicationRecord(InputInterface $input, OutputInterface $output): Application
    {
        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->applicationManager->read(Id::create((int) $id));
            } catch (SectionNotFoundException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $application = $this->getApplicationRecord($input, $output);
        $config = $input->getArgument('config');

        try {
            $applicationConfig = ApplicationConfig::create(
                Yaml::parse(
                    file_get_contents($config)
                )
            );
            $this->applicationManager->updateByConfig($applicationConfig, $application);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }

        $output->writeln('<info>Application updated!</info>');
    }
}

