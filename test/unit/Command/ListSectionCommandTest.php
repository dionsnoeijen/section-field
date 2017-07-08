<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

/**
 * @coversDefaultClass Tardigrades\Command\ListSectionCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class ListSectionCommandTest extends TestCase
{
    /**
     * @var SectionManager
     */
    private $sectionManager;

    /**
     * @var ListSectionCommand
     */
    private $listSectionCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->sectionManager = Mockery::mock(SectionManager::class);
        $this->listSectionCommand = new ListSectionCommand($this->sectionManager);
        $this->application = new Application();
        $this->application->add($this->listSectionCommand);
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
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_list_field_types()
    {
        $command = $this->application->find('sf:list-section');
        $commandTester = new CommandTester($command);

        $languages = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($languages);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/All installed Sections/',
            $commandTester->getDisplay()
        );
    }
}
