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
 * @coversDefaultClass Tardigrades\Command\DeleteFieldTypeCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteFieldTypeCommandTest extends TestCase
{
    /** @var FieldTypeManagerInterface */
    private $fieldTypeManager;

    /** @var DeleteFieldTypeCommand */
    private $deleteFieldTypeCommand;

    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->fieldTypeManager = Mockery::mock(FieldTypeManagerInterface::class);
        $this->deleteFieldTypeCommand = new DeleteFieldTypeCommand($this->fieldTypeManager);
        $this->application = new Application();
        $this->application->add($this->deleteFieldTypeCommand);
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
    public function it_should_delete_field_type_with_id_1()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFieldTypes();

        $this->fieldTypeManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->fieldTypeManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->fieldTypeManager
            ->shouldReceive('delete')
            ->once();

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Removed!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_not_delete_field_type_with_id_1_when_cancelled()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFieldTypes();

        $this->fieldTypeManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->fieldTypeManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->fieldTypeManager
            ->shouldReceive('delete')
            ->never();

        $commandTester->setInputs([1, 'n']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Cancelled, nothing deleted./',
            $commandTester->getDisplay()
        );
    }
}
