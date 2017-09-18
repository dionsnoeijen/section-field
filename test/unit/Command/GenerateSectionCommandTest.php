<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Generator\GeneratorsInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\GenerateSectionCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class GenerateSectionCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionManagerInterface|Mockery\MockInterface */
    private $sectionManager;

    /** @var GeneratorsInterface */
    private $entityGenerator;

    /** @var GenerateSectionCommand */
    private $generateSectionCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->sectionManager = Mockery::mock(SectionManagerInterface::class);
        $this->entityGenerator = Mockery::mock(GeneratorsInterface::class);
        $this->generateSectionCommand = new GenerateSectionCommand($this->sectionManager, $this->entityGenerator);
        $this->application = new Application();
        $this->application->add($this->generateSectionCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_generate_a_section()
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

        $command = $this->application->find('sf:generate-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($sections[0]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[0])
            ->once();

        $this->entityGenerator
            ->shouldReceive('getBuildMessages')
            ->once();

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Some name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Some other name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/someHandle/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/someOtherHandle/',
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
            '/namespace:My\\\\Namespace/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Available sections/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfSections()
    {
        return [
            (new Section())
                ->setId(1)
                ->setName('Some name')
                ->setHandle('someHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Section())
                ->setId(2)
                ->setName('Some other name')
                ->setHandle('someOtherHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }
}
