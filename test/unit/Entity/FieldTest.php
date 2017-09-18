<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;

/**
 * @coversDefaultClass Tardigrades\Entity\Field
 * @covers ::__construct
 * @covers ::<private>
 */
final class FieldTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Field */
    private $field;

    /** @var Collection|Mockery\MockInterface */
    private $sections;

    public function setUp()
    {
        $this->sections = Mockery::mock(Collection::class);

        $this->field = new Field($this->sections);
    }

    /**
     * @test
     * @covers ::setId
     * @covers ::getId
     */
    public function it_should_set_and_get_an_id()
    {
        $field = $this->field->setId(5);

        $this->assertSame($this->field, $field);
        $this->assertEquals(5, $this->field->getId());
    }

    /**
     * @test
     * @covers ::getIdValueObject
     */
    public function it_should_get_an_id_value_object()
    {
        $field = $this->field->setId(10);

        $this->assertEquals(Id::fromInt(10), $this->field->getIdValueObject());
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_null_asking_for_unset_id()
    {
        $this->assertEquals(null, $this->field->getId());
    }

    /**
     * @test
     * @covers ::setHandle
     * @covers ::getHandle
     */
    public function it_should_set_and_get_handle()
    {
        $handle = Handle::fromString('someHandleINeed');
        $field = $this->field->setHandle((string) $handle);

        $this->assertEquals($this->field, $field);
        $this->assertEquals($this->field->getHandle(), $handle);
    }

    /**
     * @test
     * @covers ::addFieldTranslation
     * @covers ::getFieldTranslations
     * @covers ::removeFieldTranslation
     */
    public function it_should_add_get_and_remove_a_field_translation()
    {
        $translation = (new FieldTranslation())
            ->setName('No name is to blame')
            ->setLanguage((new Language())->setI18n('nl_NL'));

        $field = new Field();
        $field->setHandle('noNameIsToBlame');
        $field->addFieldTranslation($translation);

        $this->assertSame($translation, $field->getFieldTranslations()->get(0));

        $field->removeFieldTranslation($translation);

        $this->assertNull($field->getFieldTranslations()->get(0));
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

        $this->sections->shouldReceive('contains')->twice()->with($section)->andReturn(false);
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
        $fieldType = new FieldType();

        $field = $this->field->setFieldType($fieldType);

        $this->assertSame($this->field, $field);
    }

    /**
     * @test
     * @covers ::removeFieldType
     */
    public function it_should_remove_the_field_type()
    {
        $fieldType = new FieldType();

        $field = $this->field->removeFieldType($fieldType);

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
                'name' => [
                    ['en_EN' => 'I have a field name']
                ],
                'handle' => 'someHandle',
                'label' => [
                    ['en_EN' => 'A label']
                ]
            ]
        ];

        $field = $this->field->setConfig($config);

        $this->assertEquals($this->field->getConfig(), FieldConfig::fromArray($config));
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

        $this->field->setCreated($dateTime);

        $this->assertEquals($this->field->getCreated(), $dateTime);
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

        $this->field->setUpdated($dateTime);

        $this->assertEquals($this->field->getUpdated(), $dateTime);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->field->onPrePersist();

        $created = new \DateTime("now");
        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->field
                ->getCreated()
                ->format('Y-m-d H:i'),
            $created
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->field
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
        $this->field->onPreUpdate();

        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->field
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
        $this->field->setCreated(new \DateTime());

        $this->assertInstanceOf(Created::class, $this->field->getCreatedValueObject());
        $this->assertEquals(
            $this->field->getCreatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getUpdatedValueObject
     */
    public function it_should_get_a_updated_value_object()
    {
        $this->field->setUpdated(new \DateTime());

        $this->assertEquals(
            $this->field->getUpdatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }
}
