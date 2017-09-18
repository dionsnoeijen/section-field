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
 * @coversDefaultClass Tardigrades\Command\UpdateFieldCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class UpdateFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldManagerInterface */
    private $fieldManager;

    /** @var UpdateFieldCommand */
    private $updateFieldCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->fieldManager = Mockery::mock(FieldManagerInterface::class);
        $this->updateFieldCommand = new UpdateFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->updateFieldCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_update_a_field()
    {
        $yml = <<<YML
field:
    name: foo
    handle: bar
    label: [ label ]
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:update-field');
        $commandTester = new CommandTester($command);

        $this->fieldManager
            ->shouldReceive('readAll')
            ->twice()
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
                'config' => $this->file
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
        $yml = <<<YML
field:
    name: foo
    handle: bar
    label: [ label ]
YML;

        $wrongYml = <<<YML
wrong: yml
YML;

        file_put_contents($this->file, $yml);
        $wrongConfig = vfsStream::url('home/wrong-config-file.yml');
        file_put_contents($wrongConfig, $wrongYml);


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
                'config' => $wrongConfig
            ]
        );

        $this->assertRegExp(
            '/Invalid configuration/',
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
                        ->setLabel('A field label')
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
                        ->setLabel('A field label')
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
                        ->setLabel('A field label')
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
                        ->setLabel('A field label')
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
                ->setId(3)
                ->setHandle('andAnotherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->addFieldTranslation(
                    (new FieldTranslation())
                        ->setName('And another field name')
                        ->setLabel('A field label')
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
                        ->setLabel('A field label')
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
