<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\ListFieldTypeCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ListFieldTypeCommandTest extends TestCase
{
    /**
     * @var FieldTypeManagerInterface
     */
    private $fieldTypeManager;

    /**
     * @var ListFieldTypeCommand
     */
    private $listFieldTypeCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->fieldTypeManager = Mockery::mock(FieldTypeManagerInterface::class);
        $this->listFieldTypeCommand = new ListFieldTypeCommand($this->fieldTypeManager);
        $this->application = new Application();
        $this->application->add($this->listFieldTypeCommand);
    }

    private function givenAnArrayOfFieldTypes(): array
    {
        return [
            (new FieldType())
                ->setId(1)
                ->setType('TextArea')
                ->setFullyQualifiedClassName('Super\\Qualified')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new FieldType())
                ->setId(2)
                ->setType('TextInput')
                ->setFullyQualifiedClassName('Amazing\\Input')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
        ];
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_list_field_types()
    {
        $command = $this->application->find('sf:list-field-type');
        $commandTester = new CommandTester($command);

        $fieldTypes = $this->givenAnArrayOfFieldTypes();

        $this->fieldTypeManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fieldTypes);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/TextArea/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/TextInput/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Super\\\\Qualified/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Amazing\\\\Input/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/All installed FieldTypes/',
            $commandTester->getDisplay()
        );
    }
}
