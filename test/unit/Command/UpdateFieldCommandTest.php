<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldTranslation;
use Tardigrades\Entity\FieldType;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;

/**
 * @coversDefaultClass Tardigrades\Command\UpdateFieldCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class UpdateFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var UpdateFieldCommand
     */
    private $updateFieldCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->fieldManager = Mockery::mock(FieldManager::class);
        $this->updateFieldCommand = new UpdateFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->updateFieldCommand);
    }

    private function givenAnArrayOfFields()
    {
        return [
            (new Field())
                ->setId(1)
                ->setHandle('someName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextInput')
                )
                ->addFieldTranslation(
                    (new FieldTranslation())
                        ->setName('Some field name')
                        ->setLanguage(
                            (new Language())
                                ->setI18n('en_EN')
                        )
                        ->setCreated(new \DateTime())
                        ->setUpdated(new \DateTime())
                )
                ->addFieldTranslation(
                    (new FieldTranslation())
                        ->setName('Een veldnaam')
                        ->setLanguage(
                            (new Language())
                                ->setI18n('nl_NL')
                        )
                        ->setCreated(new \DateTime())
                        ->setUpdated(new \DateTime())
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
                ->setHandle('someOtherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->addFieldTranslation(
                    (new FieldTranslation())
                        ->setName('Some other field name')
                        ->setLanguage(
                            (new Language())
                                ->setI18n('en_EN')
                        )
                        ->setCreated(new \DateTime())
                        ->setUpdated(new \DateTime())
                )
                ->addFieldTranslation(
                    (new FieldTranslation())
                        ->setName('Een andere veldnaam')
                        ->setLanguage(
                            (new Language())
                                ->setI18n('nl_NL')
                        )
                        ->setCreated(new \DateTime())
                        ->setUpdated(new \DateTime())
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
                ->setHandle('andAnotherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->addFieldTranslation(
                    (new FieldTranslation())
                        ->setName('And another field name')
                        ->setLanguage(
                            (new Language())
                                ->setI18n('en_EN')
                        )
                        ->setCreated(new \DateTime())
                        ->setUpdated(new \DateTime())
                )
                ->addFieldTranslation(
                    (new FieldTranslation())
                        ->setName('En nog een veldnaam')
                        ->setLanguage(
                            (new Language())
                                ->setI18n('nl_NL')
                        )
                        ->setCreated(new \DateTime())
                        ->setUpdated(new \DateTime())
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
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_update_a_field()
    {
        $command = $this->application->find('sf:update-field');
        $commandTester = new CommandTester($command);

        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfFields());

        $this->fieldManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfFields()[0]);

        $this->fieldManager
            ->shouldReceive('updateByConfig')
            ->once()
            ->andReturn($this->givenAnArrayOfFields()[0]);

        $commandTester->setInputs([1]);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-field-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Field updated!/',
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
        $command = $this->application->find('sf:update-field');
        $commandTester = new CommandTester($command);

        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfFields());

        $this->fieldManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfFields()[0]);

        $commandTester->setInputs([1]);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-field-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid configuration/',
            $commandTester->getDisplay()
        );
    }
}
