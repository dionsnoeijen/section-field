<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Service\SectionManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\CreateSectionCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateSectionCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionManagerInterface */
    private $sectionManager;

    /** @var CreateSectionCommand */
    private $createSectionCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->sectionManager = Mockery::mock(SectionManagerInterface::class);
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
        $yml = <<<YML
section:
    name: foo
    handle: bar
    fields: []
    default: Default
    namespace: My\Namespace
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:create-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('createByConfig')
            ->once()
            ->andReturn(new Section());

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => $this->file
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
        $yml = <<<YML
wrong: yml
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:create-section');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => $this->file
            ]
        );

        $this->assertRegExp(
            '/Invalid configuration/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     * @throws \Exception
     */
    public function it_should_fail_on_absent_config()
    {
        $command = $this->application->find('sf:create-section');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'this-file-does-not-exist'
            ]
        );

        $this->assertRegExp(
            '/Invalid configuration/',
            $commandTester->getDisplay()
        );
    }
}
