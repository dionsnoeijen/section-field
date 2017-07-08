<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager;

/**
 * @coversDefaultClass Tardigrades\Command\CreateApplicationCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateApplicationCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ApplicationManager */
    private $applicationManager;

    /** @var CreateApplicationCommand */
    private $createApplicationCommand;

    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->applicationManager = Mockery::mock(ApplicationManager::class);
        $this->createApplicationCommand = new CreateApplicationCommand($this->applicationManager);
        $this->application = new Application();
        $this->application->add($this->createApplicationCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_create_an_application_based_on_config()
    {
        $command = $this->application->find('sf:create-application');
        $commandTester = new CommandTester($command);

        $this->applicationManager
            ->shouldReceive('createByConfig')
            ->once();

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-application-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Application created!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_with_invalid_config()
    {
        $command = $this->application->find('sf:create-application');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-application-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid config./',
            $commandTester->getDisplay()
        );
    }
}
