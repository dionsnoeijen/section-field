<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Service\SectionManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\ListSectionCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ListSectionCommandTest extends TestCase
{
    /** @var SectionManagerInterface */
    private $sectionManager;

    /** @var ListSectionCommand */
    private $listSectionCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->sectionManager = Mockery::mock(SectionManagerInterface::class);
        $this->listSectionCommand = new ListSectionCommand($this->sectionManager);
        $this->application = new Application();
        $this->application->add($this->listSectionCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_list_sections()
    {
        $yml = <<<YML
section:
    name: foo
    handle: bar
    fields: []
    default: Default
    namespace: My\Namespace
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:list-section');
        $commandTester = new CommandTester($command);

        $languages = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($languages);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Some name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/someHandle/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/name:foo/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/handle:bar/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/fields:/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/default:Default/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            "/namespace:My\\\\Namespace/",
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Some other name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/someOtherHandle/',
            $commandTester->getDisplay()
        );
        $this->assertRegExp(
            '/All installed Sections/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/All installed Sections/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/All installed Sections/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfSections()
    {
        return [
            (new Section())
                ->setName('Some name')
                ->setHandle('someHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Section())
                ->setName('Some other name')
                ->setHandle('someOtherHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }
}
