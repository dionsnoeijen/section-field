<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\SectionField\Service\LanguageManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\CreateLanguageCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateLanguageCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var LanguageManagerInterface */
    private $languageManager;

    /** @var CreateLanguageCommand */
    private $createLanguageCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->languageManager = Mockery::mock(LanguageManagerInterface::class);
        $this->createLanguageCommand = new CreateLanguageCommand($this->languageManager);
        $this->application = new Application();
        $this->application->add($this->createLanguageCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_create_languages_based_on_config()
    {
        $yml = <<<YML
language: [ en-EN ]
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:create-language');
        $commandTester = new CommandTester($command);

        $this->languageManager
            ->shouldReceive('createByConfig')
            ->once();

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => $this->file
            ]
        );

        $this->assertRegExp(
            '/Languages created!/',
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
wrong: yml
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:create-language');
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
