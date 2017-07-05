<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;

/**
 * @coversDefaultClass Tardigrades\Command\DeleteFieldCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var DeleteFieldCommand
     */
    private $deleteFieldCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->fieldManager = Mockery::mock(FieldManager::class);
        $this->deleteFieldCommand = new DeleteFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->deleteFieldCommand);
    }

    private function givenAnArrayOfFields()
    {
        return [
            (new Field())
                ->setId(1)
                ->setName('Some name')
                ->setHandle('someName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextInput')
                )
                ->setConfig([
                    'field' => [
                        'name' => 'Some name',
                        'handle' => 'someName',
                    ]
                ])
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Field())
                ->setId(2)
                ->setName('Some other name')
                ->setHandle('someOtherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->setConfig([
                    'field' => [
                        'name' => 'Some other name',
                        'handle' => 'someOtherName',
                    ]
                ])
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Field())
                ->setId(3)
                ->setName('And another name')
                ->setHandle('andAnotherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->setConfig([
                    'field' => [
                        'name' => 'And another name',
                        'handle' => 'andAnotherName',
                    ]
                ])
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }

    /**
     * @test
     */
    public function it_should_delete_field_with_id_1()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFields();

        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->fieldManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->fieldManager
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
