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
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Service\ApplicationManagerInterface;
use Tardigrades\Entity\Application as ApplicationEntity;

/**
 * @coversDefaultClass Tardigrades\Command\UpdateApplicationCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class UpdateApplicationCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ApplicationManagerInterface */
    private $applicationManager;

    /** @var UpdateApplicationCommand */
    private $updateApplicationCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->applicationManager = Mockery::mock(ApplicationManagerInterface::class);
        $this->updateApplicationCommand = new UpdateApplicationCommand($this->applicationManager);
        $this->application = new Application();
        $this->application->add($this->updateApplicationCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_update_an_application_based_on_config()
    {
        $yml = <<<YML
application:
    name: foo
    handle: bar
    languages: []
YML;

        file_put_contents($this->file, $yml);

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
                'config' => $this->file
            ]
        );

        $this->assertRegExp(
            '/Application updated!/',
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
                'config' => $this->file
            ]
        );

        $this->assertRegExp(
            '/Invalid configuration/',
            $commandTester->getDisplay()
        );
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
}
