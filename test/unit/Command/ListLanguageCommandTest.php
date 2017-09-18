<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\Service\LanguageManagerInterface;
use Tardigrades\Entity\Application as ApplicationEntity;

/**
 * @coversDefaultClass Tardigrades\Command\ListLanguageCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ListLanguageCommandTest extends TestCase
{
    /** @var LanguageManagerInterface */
    private $languageManager;

    /** @var ListLanguageCommand */
    private $listLanguageCommand;

    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->languageManager = Mockery::mock(LanguageManagerInterface::class);
        $this->listLanguageCommand = new ListLanguageCommand($this->languageManager);
        $this->application = new Application();
        $this->application->add($this->listLanguageCommand);
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

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_list_field_types()
    {
        $command = $this->application->find('sf:list-language');
        $commandTester = new CommandTester($command);

        $languages = $this->givenAnArrayOfLanguages();

        $this->languageManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($languages);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/All installed languages/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/nl_NL/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/en_EN/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Application name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Another application name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Again, a name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Fffff, name/',
            $commandTester->getDisplay()
        );
    }
}
