<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Language;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager;
use Tardigrades\Entity\Application as ApplicationEntity;

/**
 * @coversDefaultClass Tardigrades\Command\UpdateApplicationCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class UpdateApplicationCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ApplicationManager */
    private $applicationManager;

    /** @var UpdateApplicationCommand */
    private $updateApplicationCommand;

    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->applicationManager = Mockery::mock(ApplicationManager::class);
        $this->updateApplicationCommand = new UpdateApplicationCommand($this->applicationManager);
        $this->application = new Application();
        $this->application->add($this->updateApplicationCommand);
    }

    private function givenAnArrayOfApplications()
    {
        return [
            (new ApplicationEntity())
                ->setId(1)
                ->setHandle('someName')
                ->setName('Some Name')
                ->addLanguage((new Language())->setI18n('nl_NL'))
                ->addLanguage((new Language())->setI18n('en_EN'))
                ->addSection((new Section())->setName('Section Name'))
                ->addSection((new Section())->setName('Another section name'))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new ApplicationEntity())
                ->setId(1)
                ->setHandle('someOtherName')
                ->setName('Some Other Name')
                ->addLanguage((new Language())->setI18n('nl_NL'))
                ->addLanguage((new Language())->setI18n('en_EN'))
                ->addSection((new Section())->setName('Section Super Name'))
                ->addSection((new Section())->setName('Another Super section name'))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }

    /**
     * @test
     */
    public function it_should_update_an_application_based_on_config()
    {
        $command = $this->application->find('sf:update-application');
        $commandTester = new CommandTester($command);

        $this->applicationManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfApplications());

        $this->applicationManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfApplications()[0]);

        $this->applicationManager
            ->shouldReceive('updateByConfig')
            ->once()
            ->andReturn($this->givenAnArrayOfApplications()[0]);

        $commandTester->setInputs([1]);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-application-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Application updated!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function it_should_fail_with_invalid_config()
    {
        $command = $this->application->find('sf:update-application');
        $commandTester = new CommandTester($command);

        $this->applicationManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfApplications());

        $this->applicationManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfApplications()[0]);

        $commandTester->setInputs([1]);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-application-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid configuration/',
            $commandTester->getDisplay()
        );
    }
}
