<?php

namespace Tardigrades\Command;

use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager;

final class DeleteFieldTypeCommandTest extends TestCase
{
    /**
     * @var FieldTypeManager
     */
    private $fieldTypeManager;

    /**
     * @var DeleteFieldTypeCommand
     */
    private $deleteFieldTypeCommand;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->fieldTypeManager = Mockery::mock(FieldTypeManager::class);
        $this->deleteFieldTypeCommand = new DeleteFieldTypeCommand($this->fieldTypeManager);
        $this->application = new Application();
        $this->application->add($this->deleteFieldTypeCommand);
    }

    /**
     * @test
     */
    public function it_should_delete_a_field_type()
    {
        $this->assertTrue(true);
    }
}
