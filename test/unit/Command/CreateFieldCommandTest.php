<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Field;
use Tardigrades\SectionField\Service\FieldManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\CreateFieldCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldManagerInterface */
    private $fieldManager;

    /** @var CreateFieldCommand */
    private $createFieldCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->fieldManager = Mockery::mock(FieldManagerInterface::class);
        $this->createFieldCommand = new CreateFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->createFieldCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_create_a_field()
    {
        $yml = <<<YML
field:
    name: foo
    handle: bar
    label: [ label ]
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:create-field');
        $commandTester = new CommandTester($command);

        $this->fieldManager
            ->shouldReceive('createByConfig')
            ->once()
            ->andReturn(new Field());

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => $this->file
            ]
        );

        $this->assertRegExp(
            '/Field created!/',
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

        $command = $this->application->find('sf:create-field');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-field-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid field config./',
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
        $command = $this->application->find('sf:create-field');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-field-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid field config./',
            $commandTester->getDisplay()
        );
    }
}
