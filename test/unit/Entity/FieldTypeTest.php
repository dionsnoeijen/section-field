<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\SectionField\ValueObject\Updated;

/**
 * @coversDefaultClass Tardigrades\Entity\FieldType
 * @covers ::__construct
 * @covers ::<private>
 */
final class FieldTypeTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Collection */
    private $fields;

    /** @var FieldType */
    private $fieldType;

    public function setUp()
    {
        $this->fields = Mockery::mock(Collection::class);
        $this->fieldType = new FieldType($this->fields);
    }

    /**
     * @test
     * @covers ::setId
     * @covers ::getId
     */
    public function it_should_set_and_get_an_id()
    {
        $field = $this->fieldType->setId(5);

        $this->assertSame($this->fieldType, $field);
        $this->assertEquals(5, $this->fieldType->getId());
    }

    /**
     * @test
     * @covers ::getIdValueObject
     */
    public function it_should_get_an_id_value_object()
    {
        $field = $this->fieldType->setId(10);

        $this->assertEquals(Id::fromInt(10), $this->fieldType->getIdValueObject());
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_null_asking_for_unset_id()
    {
        $this->assertEquals(null, $this->fieldType->getId());
    }

    /**
     * @test
     * @covers ::setType ::getType
     */
    public function it_should_set_and_get_type()
    {
        $type = Type::fromString('SuperFieldType');
        $fieldType = $this->fieldType->setType((string) $type);

        $this->assertSame($this->fieldType, $fieldType);
        $this->assertEquals($this->fieldType->getType(), $type);
    }

    /**
     * @test
     * @covers ::setFullyQualifiedClassName ::getFullyQualifiedClassName
     */
    public function it_should_set_and_get_fully_qualified_class_name()
    {
        $fullyQualifiedClassName = FullyQualifiedClassName::fromString('This\\Is\\A\\Fully\\Qualified\\Class\\Name');
        $fieldType = $this->fieldType->setFullyQualifiedClassName((string) $fullyQualifiedClassName);

        $this->assertSame($this->fieldType, $fieldType);
        $this->assertEquals(
            $this->fieldType->getFullyQualifiedClassName(),
            $fullyQualifiedClassName
        );
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

        $this->fieldType->setCreated($dateTime);

        $this->assertEquals($this->fieldType->getCreated(), $dateTime);
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

        $this->fieldType->setUpdated($dateTime);

        $this->assertEquals($this->fieldType->getUpdated(), $dateTime);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->fieldType->onPrePersist();

        $created = new \DateTime("now");
        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->fieldType
                ->getCreated()
                ->format('Y-m-d H:i'),
            $created
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->fieldType
                ->getUpdated()
                ->format('Y-m-d H:i'),
            $updated
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

        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->fieldType
                ->getUpdated()
                ->format('Y-m-d H:i'),
            $updated
                ->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getCreatedValueObject
     */
    public function it_should_get_a_created_value_object()
    {
        $this->fieldType->setCreated(new \DateTime());

        $this->assertInstanceOf(Created::class, $this->fieldType->getCreatedValueObject());
        $this->assertEquals(
            $this->fieldType->getCreatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getUpdatedValueObject
     */
    public function it_should_get_a_updated_value_object()
    {
        $this->fieldType->setUpdated(new \DateTime());

        $this->assertInstanceOf(Updated::class, $this->fieldType->getUpdatedValueObject());
        $this->assertEquals(
            $this->fieldType->getUpdatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }
}
