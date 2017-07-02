<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\EntityInterface\Field as FieldInterface;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Updated;
use TypeError;

/**
 * @coversDefaultClass Tardigrades\Entity\FieldType
 * @covers ::<private>
 * @covers ::__construct
 */
final class SectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private $fields;

    /**
     * @var Section
     */
    private $section;

    public function setUp()
    {
        $this->fields = Mockery::mock(Collection::class);
        $this->section = new Section($this->fields);
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_type_error_for_id()
    {
        $this->expectException(TypeError::class);
        $this->section->getId();
    }

    /**
     * @test
     * @covers ::setName ::getName
     */
    public function it_should_set_and_get_name()
    {
        $name = Name::create('I have a name');
        $section = $this->section->setName((string) $name);

        $this->assertSame($this->section, $section);
        $this->assertEquals($this->section->getName(), $name);
    }

    /**
     * @test
     * @covers ::setHandle ::getHandle
     */
    public function it_should_set_and_get_handle()
    {
        $handle = Handle::create('someHandleINeed');
        $section = $this->section->setHandle((string) $handle);

        $this->assertSame($this->section, $section);
        $this->assertEquals($this->section->getHandle(), $handle);
    }

    /**
     * @test
     * @covers ::addField
     */
    public function it_should_add_a_field()
    {
        $field = Mockery::mock(FieldInterface::class);

        $field->shouldReceive('addSection')->once()->with($this->section);
        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(false);
        $this->fields->shouldReceive('add')->once()->with($field);

        $section = $this->section->addField($field);

        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::removeSection
     */
    public function it_should_remove_a_field()
    {
        $field = Mockery::mock(FieldInterface::class);

        $field->shouldReceive('removeSection')->once()->with($this->section);
        $field->shouldReceive('addSection')->once()->with($this->section);

        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(false);
        $this->fields->shouldReceive('add')->once()->with($field);
        $this->fields->shouldReceive('remove')->once()->with($field);

        $this->section->addField($field);

        $fieldType = $this->section->removeField($field);

        $this->assertEquals($this->section, $fieldType);
    }

    /**
     * @test
     * @covers ::getFields
     */
    public function it_should_get_fields()
    {
        $fieldOne = new Field();
        $fieldTwo = new Field();

        $section = new Section(new ArrayCollection());

        $section->addField($fieldOne);
        $section->addField($fieldTwo);

        $fields = $section->getFields();

        $this->assertSame($fields->get(0), $fieldOne);
        $this->assertSame($fields->get(1), $fieldTwo);
    }

    /**
     * @test
     * @covers ::setConfig
     */
    public function it_should_set_the_section_config()
    {
        $config = [
            'section' => [
                'name' => 'I have a field name',
                'fields' => ['these', 'are', 'fields'],
                'slug' => ['these'],
                'default' => 'these'
            ]
        ];

        $section = $this->section->setConfig($config);

        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::getConfig
     */
    public function it_should_get_the_section_config()
    {
        $config = [
            'section' => [
                'name' => 'I have a field name',
                'fields' => ['these', 'are', 'fields'],
                'slug' => ['these'],
                'default' => 'these'
            ]
        ];

        $section = $this->section->setConfig($config);

        $this->assertEquals($this->section->getConfig(), SectionConfig::create($config));
        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::setCreated
     */
    public function it_should_set_created_date_time()
    {
        $created = new \DateTime('2017-07-02');

        $section = $this->section->setCreated($created);

        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::getCreated
     */
    public function it_should_get_created_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');
        $created = Created::create($dateTime);

        $this->section->setCreated($dateTime);

        $this->assertEquals($this->section->getCreated(), $created);
    }

    /**
     * @test
     * @covers ::setUpdated
     */
    public function it_should_set_updated_date_time()
    {
        $updated = new \DateTime('2017-07-02');

        $section = $this->section->setUpdated($updated);

        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::getUpdated
     */
    public function it_should_get_updated_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');
        $updated = Updated::create($dateTime);

        $this->section->setUpdated($dateTime);

        $this->assertEquals($this->section->getUpdated(), $updated);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->section->onPrePersist();

        $created = Created::create(new \DateTime("now"));
        $updated = Updated::create(new \DateTime("now"));

        $this->assertEquals(
            $this->section
                ->getCreated()
                ->getDateTime()
                ->format('Y-m-d H:i'),
            $created
                ->getDateTime()
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->section
                ->getUpdated()
                ->getDateTime()
                ->format('Y-m-d H:i'),
            $updated
                ->getDateTime()
                ->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::onPreUpdate
     */
    public function it_should_update_update_date_on_pre_update()
    {
        $this->section->onPreUpdate();

        $updated = Updated::create(new \DateTime("now"));

        $this->assertEquals(
            $this->section
                ->getUpdated()
                ->getDateTime()
                ->format('Y-m-d H:i'),
            $updated
                ->getDateTime()
                ->format('Y-m-d H:i')
        );
    }
}
