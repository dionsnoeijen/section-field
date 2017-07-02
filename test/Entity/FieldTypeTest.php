<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\EntityInterface\Field as FieldInterface;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\SectionField\ValueObject\Updated;
use TypeError;

/**
 * @coversDefaultClass Tardigrades\Entity\FieldType
 * @covers ::<private>
 * @covers ::__construct
 */
final class FieldTypeTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Collection
     */
    private $fields;

    /**
     * @var FieldType
     */
    private $fieldType;

    public function setUp()
    {
        $this->fields = Mockery::mock(Collection::class);
        $this->fieldType = new FieldType($this->fields);
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_type_error_for_id()
    {
        $this->expectException(TypeError::class);
        $this->fieldType->getId();
    }

    /**
     * @test
     * @covers ::setType ::getType
     */
    public function it_should_set_and_get_type()
    {
        $type = Type::create('SuperFieldType');
        $fieldType = $this->fieldType->setType((string) $type);

        $this->assertSame($this->fieldType, $fieldType);
        $this->assertEquals($this->fieldType->getType(), $type);
    }

    /**
     * @test
     * @covers ::setNamespace ::getNamespace
     */
    public function it_should_set_and_get_namespace()
    {
        $fullyQualifiedClassName = FullyQualifiedClassName::create('This\\Is\\A\\Fully\\Qualified\\Class\\Name');
        $fieldType = $this->fieldType->setNamespace((string) $fullyQualifiedClassName);

        $this->assertSame($this->fieldType, $fieldType);
        $this->assertEquals($this->fieldType->getNamespace(), $fullyQualifiedClassName);
    }

    /**
     * @test
     * @covers ::addField
     */
    public function it_should_add_a_field()
    {
        $field = Mockery::mock(FieldInterface::class);

        $field->shouldReceive('setFieldType')->once()->with($this->fieldType);
        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(false);
        $this->fields->shouldReceive('add')->once()->with($field);

        $fieldType = $this->fieldType->addField($field);

        $this->assertEquals($this->fieldType, $fieldType);
    }

    /**
     * @test
     * @covers ::removeField
     */
    public function it_should_remove_a_field()
    {
        $field = new Field();

        $this->fields
            ->shouldReceive('contains')
            ->once()
            ->with($field)
            ->andReturn(true);

        $this->fields->shouldReceive('remove')->once()->with($field);

        $fieldType = $this->fieldType->removeField($field);

        $this->assertEquals($this->fieldType, $fieldType);
    }

    /**
     * @test
     * @covers ::getFields
     */
    public function it_should_get_fields()
    {
        $fieldOne = new Field();
        $fieldTwo = new Field();

        $fieldType = new FieldType(new ArrayCollection());

        $fieldType->addField($fieldOne);
        $fieldType->addField($fieldTwo);

        $fields = $fieldType->getFields();

        $this->assertSame($fields->get(0), $fieldOne);
        $this->assertSame($fields->get(1), $fieldTwo);
    }

    /**
     * @test
     * @covers ::setCreated
     */
    public function it_should_set_created_date_time()
    {
        $created = new \DateTime('2017-07-02');

        $fieldType = $this->fieldType->setCreated($created);

        $this->assertSame($this->fieldType, $fieldType);
    }

    /**
     * @test
     * @covers ::getCreated
     */
    public function it_should_get_created_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');
        $created = Created::create($dateTime);

        $this->fieldType->setCreated($dateTime);

        $this->assertEquals($this->fieldType->getCreated(), $created);
    }

    /**
     * @test
     * @covers ::setUpdated
     */
    public function it_should_set_updated_date_time()
    {
        $updated = new \DateTime('2017-07-02');

        $fieldType = $this->fieldType->setUpdated($updated);

        $this->assertSame($this->fieldType, $fieldType);
    }

    /**
     * @test
     * @covers ::getUpdated
     */
    public function it_should_get_updated_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');
        $updated = Updated::create($dateTime);

        $this->fieldType->setUpdated($dateTime);

        $this->assertEquals($this->fieldType->getUpdated(), $updated);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->fieldType->onPrePersist();

        $created = Created::create(new \DateTime("now"));
        $updated = Updated::create(new \DateTime("now"));

        $this->assertEquals(
            $this->fieldType
                ->getCreated()
                ->getDateTime()
                ->format('Y-m-d H:i'),
            $created
                ->getDateTime()
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->fieldType
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
        $this->fieldType->onPreUpdate();

        $updated = Updated::create(new \DateTime("now"));

        $this->assertEquals(
            $this->fieldType
                ->getUpdated()
                ->getDateTime()
                ->format('Y-m-d H:i'),
            $updated
                ->getDateTime()
                ->format('Y-m-d H:i')
        );
    }
}
