<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\SectionField\Service\ApplicationManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\CreateApplicationCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateApplicationCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ApplicationManagerInterface */
    private $applicationManager;

    /** @var CreateApplicationCommand */
    private $createApplicationCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->applicationManager = Mockery::mock(ApplicationManagerInterface::class);
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
        $yml = <<<YML
application:
    name: foo
    handle: bar
    languages: []
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:create-application');
        $commandTester = new CommandTester($command);

        $this->applicationManager
            ->shouldReceive('createByConfig')
            ->once();

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => $this->file
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
        $yml = <<<YML
invalid:
    yml:
YML;

        file_put_contents($this->file, $yml);
        $command = $this->application->find('sf:create-application');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => $this->file
            ]
        );

        $this->assertRegExp(
            '/Invalid config./',
            $commandTester->getDisplay()
        );
    }
}
