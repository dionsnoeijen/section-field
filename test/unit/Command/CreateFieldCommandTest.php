<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Field;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;

/**
 * @coversDefaultClass Tardigrades\Command\CreateFieldCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var CreateFieldCommand
     */
    private $createFieldCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->fieldManager = Mockery::mock(FieldManager::class);
        $this->createFieldCommand = new CreateFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->createFieldCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_create_a_field()
    {
        $command = $this->application->find('sf:create-field');
        $commandTester = new CommandTester($command);

        $this->fieldManager
            ->shouldReceive('createByConfig')
            ->once()
            ->andReturn(new Field());

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-field-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Field created!/',
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
        $command = $this->application->find('sf:create-field');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'config' => 'some-erroneous-field-config-file.yml'
            ]
        );

        $this->assertRegExp(
            '/Invalid field config./',
            $commandTester->getDisplay()
        );
    }
}

/**
 * Stubbed php method
 * @todo I have a problem with overriding file_get_contents
 * multiple times. Therefore I have to add the section config
 * cases here... weird
 * @param string $filename
 * @return string
 */
function file_get_contents($filename)
{
    switch ($filename) {
        case 'some-field-config-file.yml':
            return <<<EOT
field:
    name:
        - nl_NL: Body
        - en_EN: Body
    handle: body
    label:
        - nl_NL: Geef lichaam
        - en_EN: Give body
    type: RichTextArea
EOT;
        case 'some-erroneous-field-config-file.yml':
            return <<<EOT
field:
    type: IAmWrongBecauseIHaveNoName
EOT;
        case 'some-section-config-file.yml':
            return <<<EOT
section:
    name: I Have a name
    fields:
        - and
        - some
        - fields
    slug: [and, some]
    default: and
EOT;
        case 'some-erroneous-section-config-file.yml':
            return <<<EOT
section:
    name: I have a name but no fields
EOT;
        case 'some-language-config-file.yml':
            return <<<EOT
language:
    - nl_NL
    - en_EN
EOT;
        case 'some-erroneous-language-config-file.yml':
            return <<<EOT
error:
    - nothing
EOT;
        case 'some-application-config-file.yml':
            return <<<EOT
application:
    name: Blog
    handle: blog
    languages:
        - nl_NL
        - en_EN
EOT;
        case 'some-erroneous-application-config-file.yml':
            return <<<EOT
application:
    no: Thing
EOT;
    }
    return 'no';
}
