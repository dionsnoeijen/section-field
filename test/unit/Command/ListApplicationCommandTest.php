<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Language;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\SectionFieldInterface\ApplicationManager;
use Tardigrades\Entity\Application as ApplicationEntity;

/**
 * @coversDefaultClass Tardigrades\Command\ListApplicationCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class ListApplicationCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ApplicationManager|Mockery\MockInterface
     */
    private $applicationManager;

    /**
     * @var ListApplicationCommand
     */
    private $listApplicationCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->applicationManager = Mockery::mock(ApplicationManager::class);
        $this->listApplicationCommand = new ListApplicationCommand($this->applicationManager);
        $this->application = new Application();
        $this->application->add($this->listApplicationCommand);
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

    /**
     * @test
     */
    public function it_should_show_a_list_of_two_applications()
    {
        $command = $this->application->find('sf:list-application');
        $commandTester = new CommandTester($command);

        $this->applicationManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfApplications());

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/All installed Applications/',
            $commandTester->getDisplay()
        );
    }
}
