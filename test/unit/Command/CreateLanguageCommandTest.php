<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\SectionField\SectionFieldInterface\LanguageManager;

/**
 * @coversDefaultClass Tardigrades\Command\CreateLanguageCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateLanguageCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var LanguageManager */
    private $languageManager;

    /** @var CreateLanguageCommand */
    private $createLanguageCommand;

    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->languageManager = Mockery::mock(LanguageManager::class);
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
        $command = $this->application->find('sf:create-language');
        $commandTester = new CommandTester($command);

        $this->languageManager
            ->shouldReceive('createByConfig')
            ->once();

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-language-config-file.yml'
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
        $command = $this->application->find('sf:create-language');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-language-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid config./',
            $commandTester->getDisplay()
        );
    }
}
