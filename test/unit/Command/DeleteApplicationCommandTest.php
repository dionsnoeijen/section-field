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
 * @coversDefaultClass Tardigrades\Command\DeleteApplicationCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteApplicationCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ApplicationManagerInterface|Mockery\MockInterface */
    private $applicationManager;

    /** @var DeleteApplicationCommand */
    private $deleteApplicationCommand;

    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->applicationManager = Mockery::mock(ApplicationManagerInterface::class);
        $this->deleteApplicationCommand = new DeleteApplicationCommand($this->applicationManager);
        $this->application = new Application();
        $this->application->add($this->deleteApplicationCommand);
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
    public function it_should_delete_field_with_id_1()
    {
        $command = $this->application->find('sf:delete-application');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfApplications();

        $this->applicationManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->applicationManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->applicationManager
            ->shouldReceive('delete')
            ->once();

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Removed!/',
            $commandTester->getDisplay()
        );
    }
}
