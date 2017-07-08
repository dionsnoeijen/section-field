<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Language;
use Tardigrades\Entity\Application as ApplicationEntity;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * @coversDefaultClass Tardigrades\Command\DeleteSectionCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteSectionCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SectionManager|Mockery\MockInterface
     */
    private $sectionManager;

    /**
     * @var DeleteSectionCommand
     */
    private $deleteSectionCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->sectionManager = Mockery::mock(SectionManager::class);
        $this->deleteSectionCommand = new DeleteSectionCommand($this->sectionManager);
        $this->application = new Application();
        $this->application->add($this->deleteSectionCommand);
    }

    private function givenAnArrayOfSections()
    {
        return [
            (new Section())
                ->setName('Some name')
                ->setHandle('someHandle')
                ->setConfig(Yaml::parse(file_get_contents('some-section-config-file.yml')))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Section())
                ->setName('Some other name')
                ->setHandle('someOtherHandle')
                ->setConfig(Yaml::parse(file_get_contents('some-section-config-file.yml')))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }

    /**
     * @test
     */
    public function it_should_delete_section_with_id_1()
    {
        $command = $this->application->find('sf:delete-section');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->sectionManager
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
