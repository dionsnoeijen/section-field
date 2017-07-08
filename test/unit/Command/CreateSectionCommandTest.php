<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

/**
 * @coversDefaultClass Tardigrades\Command\CreateSectionCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateSectionCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SectionManager
     */
    private $sectionManager;

    /**
     * @var CreateSectionCommand
     */
    private $createSectionCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->sectionManager = Mockery::mock(SectionManager::class);
        $this->createSectionCommand = new CreateSectionCommand($this->sectionManager);
        $this->application = new Application();
        $this->application->add($this->createSectionCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_create_a_section()
    {
        $command = $this->application->find('sf:create-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('createByConfig')
            ->once()
            ->andReturn(new Section());

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-section-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Section created!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_on_incorrect_config()
    {
        $command = $this->application->find('sf:create-section');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-section-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid configuration/',
            $commandTester->getDisplay()
        );
    }
}
