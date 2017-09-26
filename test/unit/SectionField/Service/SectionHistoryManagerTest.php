<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Mockery;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\Language;
use Tardigrades\Entity\SectionHistory;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DoctrineSectionHistoryManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class SectionHistoryManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var DoctrineSectionHistoryManager
     */
    private $sectionHistoryManager;

    /**
     * @var EntityManagerInterface|Mockery\MockInterface
     */
    private $entityManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->sectionHistoryManager = new DoctrineSectionHistoryManager(
            $this->entityManager
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new SectionHistory();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->sectionHistoryManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_a_language()
    {
        $entity = new Language();
        $id = Id::fromInt(1);
        $languageRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $language = $this->languageManager->read($id);

        $this->assertEquals($language, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_throw_an_exception()
    {
        $id = Id::fromInt(20);
        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(LanguageNotFoundException::class);

        $this->languageManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_languages()
    {
        $languageOne = new Language();
        $languageTwo = new Language();

        $languageRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([
                $languageOne,
                $languageTwo
            ]);

        $this->assertEquals(
            $this->languageManager->readAll(),
            [
                $languageOne,
                $languageTwo
            ]
        );
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_languages_and_throw_an_exception()
    {
        $languageRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(LanguageNotFoundException::class);

        $this->languageManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_language()
    {
        $this->entityManager->shouldReceive('flush')->once();

        $this->languageManager->update();
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_a_language()
    {
        $language = new Language();
        $this->entityManager->shouldReceive('remove')->once()->with($language);
        $this->entityManager->shouldReceive('flush')->once();

        $this->languageManager->delete($language);
    }

    /**
     * @test
     * @covers ::readByI18n
     */
    public function it_should_read_by_i18n()
    {
        $i18n = I18n::fromString('nl_NL');

        $languageRepository = Mockery::mock(ObjectRepository::class);

        $language = (new Language())->setI18n((string) $i18n);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('findOneBy')
            ->once()
            ->with(['i18n' => (string) $i18n])
            ->andReturn($language);

        $returnedLanguage = $this->languageManager->readByI18n($i18n);

        $this->assertEquals($language->getI18n(), $returnedLanguage->getI18n());
    }
}
