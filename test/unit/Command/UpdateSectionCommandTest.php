<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

/**
 * @coversDefaultClass Tardigrades\Command\UpdateSectionCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class UpdateSectionCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SectionManager
     */
    private $sectionManager;

    /**
     * @var UpdateSectionCommand
     */
    private $updateSectionCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->sectionManager = Mockery::mock(SectionManager::class);
        $this->updateSectionCommand = new UpdateSectionCommand($this->sectionManager);
        $this->application = new Application();
        $this->application->add($this->updateSectionCommand);
    }

    private function givenAnArrayOfSections()
    {
        return [
            (new Section())
                ->setName('Some name')
                ->setHandle('someHandle')
                ->setConfig(
                    Yaml::parse(
                        file_get_contents('some-section-config-file.yml')
                    )
                )
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Section())
                ->setName('Some other name')
                ->setHandle('someOtherHandle')
                ->setConfig(
                    Yaml::parse(
                        file_get_contents('some-section-config-file.yml')
                    )
                )
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_update_a_section()
    {
        $command = $this->application->find('sf:update-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfSections());

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfSections()[0]);

        $this->sectionManager
            ->shouldReceive('updateByConfig')
            ->once()
            ->andReturn($this->givenAnArrayOfSections()[0]);

        $commandTester->setInputs([1]);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-section-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Section updated!/',
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
        $command = $this->application->find('sf:update-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfSections());

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfSections()[0]);

        $commandTester->setInputs([1]);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-section-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid configuration/',
            $commandTester->getDisplay()
        );
    }
}
