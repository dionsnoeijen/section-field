<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Mockery;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\Application;
use Tardigrades\SectionField\ValueObject\Id;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\ApplicationManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class ApplicationManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ApplicationManager
     */
    private $applicationManager;

    /**
     * @var EntityManagerInterface|Mockery\MockInterface
     */
    private $entityManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->applicationManager = new ApplicationManager(
            $this->entityManager
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new Application();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->applicationManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_an_application()
    {
        $entity = new Application();
        $id = Id::create(1);
        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $application = $this->applicationManager->read($id);

        $this->assertEquals($application, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_throw_an_exception()
    {
        $id = Id::create(20);
        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(ApplicationNotFoundException::class);

        $this->applicationManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_applications()
    {
        $applicationOne = new Application();
        $applicationTwo = new Application();

        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$applicationOne, $applicationTwo]);

        $this->assertEquals(
            $this->applicationManager->readAll(),
            [$applicationOne, $applicationTwo]
        );
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_applications_and_throw_an_exception()
    {
        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(ApplicationNotFoundException::class);

        $this->applicationManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_an_application()
    {
        $this->entityManager->shouldReceive('flush')->once();

        $this->applicationManager->update();
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_an_application()
    {
        $application = new Application();
        $this->entityManager->shouldReceive('remove')->once()->with($application);
        $this->entityManager->shouldReceive('flush')->once();

        $this->applicationManager->delete($application);
    }
}

