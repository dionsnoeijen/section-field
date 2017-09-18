<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Language;
use Tardigrades\Entity\Application as ApplicationEntity;
use Tardigrades\SectionField\Service\LanguageManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\UpdateLanguageCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class UpdateLanguageCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var LanguageManagerInterface */
    private $languageManager;

    /** @var UpdateLanguageCommand */
    private $updateLanguageCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->languageManager = Mockery::mock(LanguageManagerInterface::class);
        $this->updateLanguageCommand = new UpdateLanguageCommand($this->languageManager);
        $this->application = new Application();
        $this->application->add($this->updateLanguageCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_update_languages_based_on_config()
    {
        $yml = <<<YML
language: [ en-EN ]
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:update-language');
        $commandTester = new CommandTester($command);

        $this->languageManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfLanguages());

        $this->languageManager
            ->shouldReceive('updateByConfig')
            ->once();

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => $this->file
            ]
        );

        $this->assertRegExp(
            '/Languages updated!/',
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

        $command = $this->application->find('sf:update-language');
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

    private function givenAnArrayOfLanguages(): array
    {
        return [
            (new Language())
                ->setId(1)
                ->setI18n('nl_NL')
                ->addApplication(
                    (new ApplicationEntity())
                        ->setId(1)
                        ->setName('Application name')
                )
                ->addApplication(
                    (new ApplicationEntity())
                        ->setId(2)
                        ->setName('Another application name')
                )
                ->setUpdated(new \DateTime())
                ->setCreated(new \DateTime()),
            (new Language())
                ->setId(2)
                ->setI18n('en_EN')
                ->addApplication(
                    (new ApplicationEntity())
                        ->setId(1)
                        ->setName('Again, a name')
                )
                ->addApplication(
                    (new ApplicationEntity())
                        ->setId(2)
                        ->setName('Fffff, name')
                )
                ->setUpdated(new \DateTime())
                ->setCreated(new \DateTime()),
        ];
    }
}
