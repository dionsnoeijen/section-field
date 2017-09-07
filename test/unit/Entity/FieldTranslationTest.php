<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;

/**
 * @coversDefaultClass Tardigrades\Entity\FieldTranslation
 * @covers ::<private>
 */
final class FieldTranslationTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldTranslation */
    private $fieldTranslation;

    public function setUp()
    {
        $this->fieldTranslation = new FieldTranslation();
    }

    /**
     * @test
     * @covers ::setId
     * @covers ::getId
     */
    public function it_should_set_and_get_an_id()
    {
        $fieldTranslation = $this->fieldTranslation->setId(5);

        $this->assertSame($this->fieldTranslation, $fieldTranslation);
        $this->assertEquals(5, $this->fieldTranslation->getId());
    }

    /**
     * @test
     * @covers ::getIdValueObject
     */
    public function it_should_get_an_id_value_object()
    {
        $this->fieldTranslation->setId(10);

        $this->assertEquals(Id::fromInt(10), $this->fieldTranslation->getIdValueObject());
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_null_asking_for_unset_id()
    {
        $this->assertEquals(null, $this->fieldTranslation->getId());
    }

    /**
     * @test
     * @covers ::setName
     * @covers ::getName
     */
    public function it_should_set_and_get_name()
    {
        $name = Name::fromString('someHandleINeed');
        $fieldTranslation = $this->fieldTranslation->setName((string) $name);

        $this->assertEquals($this->fieldTranslation, $fieldTranslation);
        $this->assertEquals($this->fieldTranslation->getName(), $name);
    }

    /**
     * @test
     * @covers ::setCreated
     */
    public function it_should_set_created_date_time()
    {
        $created = new \DateTime('2017-07-02');

        $field = $this->fieldTranslation->setCreated($created);

        $this->assertSame($this->fieldTranslation, $field);
    }

    /**
     * @test
     * @covers ::getCreated
     */
    public function it_should_get_created_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->fieldTranslation->setCreated($dateTime);

        $this->assertEquals($this->fieldTranslation->getCreated(), $dateTime);
    }

    /**
     * @test
     * @covers ::setUpdated
     */
    public function it_should_set_updated_date_time()
    {
        $updated = new \DateTime('2017-07-02');

        $field = $this->fieldTranslation->setUpdated($updated);

        $this->assertSame($this->fieldTranslation, $field);
    }

    /**
     * @test
     * @covers ::getUpdated
     */
    public function it_should_get_updated_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->fieldTranslation->setUpdated($dateTime);

        $this->assertEquals($this->fieldTranslation->getUpdated(), $dateTime);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->fieldTranslation->onPrePersist();

        $created = new \DateTime("now");
        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->fieldTranslation
                ->getCreated()
                ->format('Y-m-d H:i'),
            $created
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->fieldTranslation
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
        $this->fieldTranslation->onPreUpdate();

        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->fieldTranslation
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
        $this->fieldTranslation->setCreated(new \DateTime());

        $this->assertInstanceOf(Created::class, $this->fieldTranslation->getCreatedValueObject());
        $this->assertEquals(
            $this->fieldTranslation->getCreatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getUpdatedValueObject
     */
    public function it_should_get_a_updated_value_object()
    {
        $this->fieldTranslation->setUpdated(new \DateTime());

        $this->assertEquals(
            $this->fieldTranslation->getUpdatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }
}
