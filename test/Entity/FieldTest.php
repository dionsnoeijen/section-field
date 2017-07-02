<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\EntityInterface\Field as FieldInterface;
use Tardigrades\Entity\EntityInterface\FieldType as FieldTypeInterface;
use Tardigrades\Entity\EntityInterface\Section as SectionInterface;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Updated;
use TypeError;

/**
 * @coversDefaultClass Tardigrades\Entity\Field
 * @covers ::<private>
 * @covers ::__construct
 */
final class FieldTest extends TestCase
{
    /**
     * @var FieldInterface
     */
    private $field;

    /**
     * @var Collection
     */
    private $sections;

    public function setUp()
    {
        $this->sections = Mockery::mock(Collection::class);

        $this->field = new Field($this->sections);
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_type_error_for_id()
    {
        $this->expectException(TypeError::class);
        $this->field->getId();
    }

    /**
     * @test
     * @covers ::setName ::getName
     */
    public function it_should_set_and_get_name()
    {
        $name = Name::create('I have a name');
        $field = $this->field->setName($name);

        $this->assertEquals($this->field, $field);
        $this->assertEquals($this->field->getName(), $name);
    }

    /**
     * @test
     * @covers ::setHandle ::getHandle
     */
    public function it_should_set_and_get_handle()
    {
        $handle = Handle::create('someHandleINeed');
        $field = $this->field->setHandle((string) $handle);

        $this->assertEquals($this->field, $field);
        $this->assertEquals($this->field->getHandle(), $handle);
    }

    /**
     * @test
     * @covers ::addSection
     */
    public function it_should_add_a_section()
    {
        $section = Mockery::mock(SectionInterface::class);

        $section->shouldReceive('addField')->once()->with($this->field);
        $this->sections->shouldReceive('contains')->once()->with($section)->andReturn(false);
        $this->sections->shouldReceive('add')->once()->with($section);

        $field = $this->field->addSection($section);

        $this->assertEquals($this->field, $field);
    }

    /**
     * @test
     * @covers ::removeSection
     */
    public function it_should_remove_a_section()
    {
        $section = Mockery::mock(Section::class);

        $section->shouldReceive('removeField')->once()->with($this->field);
        $section->shouldReceive('addField')->once()->with($this->field);

        $this->sections->shouldReceive('contains')->once()->with($section)->andReturn(false);
        $this->sections->shouldReceive('add')->once()->with($section);
        $this->sections->shouldReceive('remove')->once()->with($section);

        $this->field->addSection($section);

        $field = $this->field->removeSection($section);

        $this->assertEquals($this->field, $field);
    }

    /**
     * @test
     * @covers ::getSections
     */
    public function it_should_get_sections()
    {
        $sectionOne = new Section();
        $sectionTwo = new Section();

        $field = new Field(new ArrayCollection());

        $field->addSection($sectionOne);
        $field->addSection($sectionTwo);

        $sections = $field->getSections();

        $this->assertSame($sections->get(0), $sectionOne);
        $this->assertSame($sections->get(1), $sectionTwo);
    }

    /**
     * @test
     * @covers ::setFieldType
     */
    public function it_should_set_the_field_type()
    {
        $fieldType = Mockery::mock(FieldTypeInterface::class);

        $fieldType->shouldReceive('addField')->once()->with($this->field);

        $field = $this->field->setFieldType($fieldType);

        $this->assertSame($this->field, $field);
    }

    /**
     * @test
     * @covers ::getFieldType
     */
    public function it_should_get_the_field_type()
    {
        $field = new Field(new ArrayCollection());
        $fieldType = new FieldType();
        $field->setFieldType($fieldType);

        $this->assertSame($field->getFieldType(), $fieldType);
    }

    /**
     * @test
     * @covers ::setConfig
     */
    public function it_should_set_the_field_config()
    {
        $config = [
            'field' => [
                'name' => 'I have a field name'
            ]
        ];

        $field = $this->field->setConfig($config);

        $this->assertSame($this->field, $field);
    }

    /**
     * @test
     * @covers ::getConfig
     */
    public function it_should_get_the_field_config()
    {
        $config = [
            'field' => [
                'name' => 'I have a field name'
            ]
        ];

        $field = $this->field->setConfig($config);

        $this->assertEquals($this->field->getConfig(), FieldConfig::create($config));
        $this->assertSame($this->field, $field);
    }

    /**
     * @test
     * @covers ::setCreated
     */
    public function it_should_set_created_date_time()
    {
        $created = new \DateTime('2017-07-02');

        $field = $this->field->setCreated($created);

        $this->assertSame($this->field, $field);
    }

    /**
     * @test
     * @covers ::getCreated
     */
    public function it_should_get_created_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');
        $created = Created::create($dateTime);

        $this->field->setCreated($dateTime);

        $this->assertEquals($this->field->getCreated(), $created);
    }

    /**
     * @test
     * @covers ::setUpdated
     */
    public function it_should_set_updated_date_time()
    {
        $updated = new \DateTime('2017-07-02');

        $field = $this->field->setUpdated($updated);

        $this->assertSame($this->field, $field);
    }

    /**
     * @test
     * @covers ::getUpdated
     */
    public function it_should_get_updated_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');
        $updated = Updated::create($dateTime);

        $this->field->setUpdated($dateTime);

        $this->assertEquals($this->field->getUpdated(), $updated);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->field->onPrePersist();

        $created = Created::create(new \DateTime("now"));
        $updated = Updated::create(new \DateTime("now"));

        $this->assertEquals(
            $this->field
                ->getCreated()
                ->getDateTime()
                ->format('Y-m-d H:i'),
            $created
                ->getDateTime()
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->field
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
        $this->field->onPreUpdate();

        $updated = Updated::create(new \DateTime("now"));

        $this->assertEquals(
            $this->field
                ->getUpdated()
                ->getDateTime()
                ->format('Y-m-d H:i'),
            $updated
                ->getDateTime()
                ->format('Y-m-d H:i')
        );
    }
}
