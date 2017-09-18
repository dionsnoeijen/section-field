<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;

/**
 * @coversDefaultClass Tardigrades\Entity\Application
 * @covers ::__construct
 * @covers ::<private>
 */
final class ApplicationTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Application */
    private $application;

    /** @var Collection|Mockery\MockInterface */
    private $languages;

    public function setUp()
    {
        $this->languages = Mockery::mock(Collection::class);

        $this->application = new Application($this->languages);
    }

    /**
     * @test
     * @covers ::setId
     * @covers ::getId
     */
    public function it_should_set_and_get_an_id()
    {
        $application = $this->application->setId(5);

        $this->assertSame($this->application, $application);
        $this->assertEquals(5, $this->application->getId());
    }

    /**
     * @test
     * @covers ::getIdValueObject
     */
    public function it_should_get_an_id_value_object()
    {
        $application = $this->application->setId(10);

        $this->assertEquals(Id::fromInt(10), $this->application->getIdValueObject());
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_null_asking_for_unset_id()
    {
        $this->assertEquals(null, $this->application->getId());
    }

    /**
     * @test
     * @covers ::setHandle
     * @covers ::getHandle
     */
    public function it_should_set_and_get_handle()
    {
        $handle = Handle::fromString('someHandleINeed');
        $application = $this->application->setHandle((string)$handle);

        $this->assertEquals($this->application, $application);
        $this->assertEquals($this->application->getHandle(), $handle);
    }

    /**
     * @test
     * @covers ::addLanguage
     * @covers ::removeLanguage
     * @covers ::getLanguages
     */
    public function it_should_add_languages_get_them_and_remove_one()
    {
        $application = new Application();

        $nl = (new Language())->setI18n('nl_NL');

        $application->addLanguage($nl);
        $application->addLanguage((new Language())->setI18n('en_EN'));

        $this->assertEquals($application->getLanguages()->count(), 2);

        $application->removeLanguage($nl);

        $this->assertEquals($application->getLanguages()->count(), 1);
        $this->assertEquals((string) $application->getLanguages()->get(1)->getI18n(), 'en_EN');
    }

    /**
     * @test
     * @covers ::addSection
     * @covers ::removeSection
     * @covers ::getSections
     */
    public function it_should_add_sections_get_and_remove_one()
    {
        $application = new Application();

        $section = (new Section())->setName('Amazing');

        $application
            ->addSection($section)
            ->addSection((new Section())->setName('Discoveries'));

        $this->assertEquals($application->getSections()->count(), 2);

        $application->removeSection($section);

        $this->assertEquals($application->getSections()->count(), 1);
        $this->assertEquals((string) $application->getSections()->get(1)->getName(), 'Discoveries');
    }

    /**
     * @test
     * @covers ::setCreated
     */
    public function it_should_set_created_date_time()
    {
        $created = new \DateTime('2017-07-02');

        $application = $this->application->setCreated($created);

        $this->assertSame($this->application, $application);
    }

    /**
     * @test
     * @covers ::getCreated
     */
    public function it_should_get_created_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->application->setCreated($dateTime);

        $this->assertEquals($this->application->getCreated(), $dateTime);
    }

    /**
     * @test
     * @covers ::setUpdated
     */
    public function it_should_set_updated_date_time()
    {
        $updated = new \DateTime('2017-07-02');

        $application = $this->application->setUpdated($updated);

        $this->assertSame($this->application, $application);
    }

    /**
     * @test
     * @covers ::getUpdated
     */
    public function it_should_get_updated_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->application->setUpdated($dateTime);

        $this->assertEquals($this->application->getUpdated(), $dateTime);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->application->onPrePersist();

        $created = new \DateTime("now");
        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->application
                ->getCreated()
                ->format('Y-m-d H:i'),
            $created
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->application
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
        $this->application->onPreUpdate();

        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->application
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
        $this->application->setCreated(new \DateTime());

        $this->assertInstanceOf(Created::class, $this->application->getCreatedValueObject());
        $this->assertEquals(
            $this->application->getCreatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getUpdatedValueObject
     */
    public function it_should_get_a_updated_value_object()
    {
        $this->application->setUpdated(new \DateTime());

        $this->assertEquals(
            $this->application->getUpdatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }
}
