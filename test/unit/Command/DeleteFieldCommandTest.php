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
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldTranslation;
use Tardigrades\Entity\FieldType;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\Service\FieldManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\DeleteFieldCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldManagerInterface */
    private $fieldManager;

    /** @var DeleteFieldCommand */
    private $deleteFieldCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->fieldManager = Mockery::mock(FieldManagerInterface::class);
        $this->deleteFieldCommand = new DeleteFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->deleteFieldCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_delete_field_with_id_1()
    {
        $yml = <<<YML
field:
    name: foo
    handle: bar
    label: [ label ]
YML;

        file_put_contents($this->file, $yml);

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
        $commandTester->execute([
            'command' => $command->getName()
        ]);

        $this->assertRegExp(
            '/Removed!/',
            $commandTester->getDisplay()
        );
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
                        ->setLabel('Dit is een label')
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
                        ->setLabel('Dit is een label')
                        ->setLanguage(
                            (new Language())
                                ->setI18n('nl_NL')
                        )
                        ->setCreated(new \DateTime())
                        ->setUpdated(new \DateTime())
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
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
                        ->setLabel('Dit is een label')
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
                        ->setLabel('Dit is een label')
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
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
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
                        ->setLabel('Dit is een label')
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
                        ->setLabel('Dit is een label')
                        ->setLanguage(
                            (new Language())
                                ->setI18n('nl_NL')
                        )
                        ->setCreated(new \DateTime())
                        ->setUpdated(new \DateTime())
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }
}
