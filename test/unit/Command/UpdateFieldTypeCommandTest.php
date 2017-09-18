<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\UpdateFieldTypeCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class UpdateFieldTypeCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldTypeManagerInterface */
    private $fieldTypeManager;

    /** @var UpdateFieldTypeCommand */
    private $updateFieldTypeCommand;

    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->fieldTypeManager = Mockery::mock(FieldTypeManagerInterface::class);
        $this->updateFieldTypeCommand = new UpdateFieldTypeCommand($this->fieldTypeManager);
        $this->application = new Application();
        $this->application->add($this->updateFieldTypeCommand);
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
    public function it_should_update_a_field_type()
    {
        $command = $this->application->find('sf:update-field-type');
        $commandTester = new CommandTester($command);

        $this->fieldTypeManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfFieldTypes());

        $this->fieldTypeManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfFieldTypes()[0]);

        $this->fieldTypeManager
            ->shouldReceive('update')
            ->once();

        $commandTester->setInputs([1, 'Totally\\New\\Fully\\Qualified\\Class\\Name', 'y']);
        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $this->assertRegExp(
            '/FieldTypeInterface Updated!/',
            $commandTester->getDisplay()
        );
    }
}
