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
 * @coversDefaultClass Tardigrades\Command\ListFieldCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class ListFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var ListFieldCommand
     */
    private $listFieldCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->fieldManager = Mockery::mock(FieldManager::class);
        $this->listFieldCommand = new ListFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->listFieldCommand);
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
    public function it_should_list_fields()
    {
        $command = $this->application->find('sf:list-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFields();

        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/All installed Fields/',
            $commandTester->getDisplay()
        );
    }
}
