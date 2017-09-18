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
use Tardigrades\SectionField\Service\ApplicationManagerInterface;
use Tardigrades\Entity\Application as ApplicationEntity;

/**
 * @coversDefaultClass Tardigrades\Command\ListApplicationCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ListApplicationCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ApplicationManagerInterface|Mockery\MockInterface */
    private $applicationManager;

    /** @var ListApplicationCommand */
    private $listApplicationCommand;

    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->applicationManager = Mockery::mock(ApplicationManagerInterface::class);
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
     * @covers ::configure
     * @covers ::execute
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

        $this->assertRegExp(
            '/someName/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Some Name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Section Name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Another section name/',
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
            '/someOtherName/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Some Other Name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Section Super Name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Another Super section name/',
            $commandTester->getDisplay()
        );
    }
}
