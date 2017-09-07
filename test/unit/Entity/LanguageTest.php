<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Updated;

/**
 * @coversDefaultClass Tardigrades\Entity\Language
 * @covers ::<private>
 */
final class LanguageTest extends TestCase
{
    /** @var Language */
    private $language;

    public function setUp()
    {
        $this->language = new Language();
    }

    /**
     * @test
     * @covers ::setId
     * @covers ::getId
     */
    public function it_should_set_and_get_an_id()
    {
        $field = $this->language->setId(5);

        $this->assertSame($this->language, $field);
        $this->assertEquals(5, $this->language->getId());
    }

    /**
     * @test
     * @covers ::getIdValueObject
     */
    public function it_should_get_an_id_value_object()
    {
        $field = $this->language->setId(10);

        $this->assertEquals(Id::fromInt(10), $this->language->getIdValueObject());
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_null_asking_for_unset_id()
    {
        $this->assertEquals(null, $this->language->getId());
    }

    /**
     * @test
     * @covers ::setI18n ::getI18n
     */
    public function it_should_set_and_get_i18n()
    {
        $language = $this->language->setI18n('nl_NL');

        $this->assertEquals(I18n::fromString('nl_NL'), $language->getI18n());
    }

    /**
     * @test
     * @covers ::addApplication
     * @covers ::getApplications
     * @covers ::removeApplication
     */
    public function it_should_add_get_and_remove_and_application()
    {
        $application = (new Application())->addLanguage($this->language);

        $this->language->addApplication($application);

        $this->assertEquals($this->language->getApplications()->get(0), $application);

        $this->language->removeApplication($application);

        $this->assertEquals($this->language->getApplications()->count(), 0);
    }

    /**
     * @test
     * @covers ::setCreated
     */
    public function it_should_set_created_date_time()
    {
        $created = new \DateTime('2017-07-02');

        $language = $this->language->setCreated($created);

        $this->assertSame($this->language, $language);
    }

    /**
     * @test
     * @covers ::getCreated
     */
    public function it_should_get_created_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->language->setCreated($dateTime);

        $this->assertEquals($this->language->getCreated(), $dateTime);
    }

    /**
     * @test
     * @covers ::setUpdated
     */
    public function it_should_set_updated_date_time()
    {
        $updated = new \DateTime('2017-07-02');

        $language = $this->language->setUpdated($updated);

        $this->assertSame($this->language, $language);
    }

    /**
     * @test
     * @covers ::getUpdated
     */
    public function it_should_get_updated_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->language->setUpdated($dateTime);

        $this->assertEquals($this->language->getUpdated(), $dateTime);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->language->onPrePersist();

        $created = new \DateTime("now");
        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->language
                ->getCreated()
                ->format('Y-m-d H:i'),
            $created
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->language
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
        $this->language->onPreUpdate();

        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->language
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
        $this->language->setCreated(new \DateTime());

        $this->assertInstanceOf(Created::class, $this->language->getCreatedValueObject());
        $this->assertEquals(
            $this->language->getCreatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getUpdatedValueObject
     */
    public function it_should_get_a_updated_value_object()
    {
        $this->language->setUpdated(new \DateTime());

        $this->assertInstanceOf(Updated::class, $this->language->getUpdatedValueObject());
        $this->assertEquals(
            $this->language->getUpdatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }
}
