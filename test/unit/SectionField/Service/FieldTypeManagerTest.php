<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DoctrineFieldTypeManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class FieldTypeManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var DoctrineFieldTypeManager
     */
    private $fieldTypeManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->fieldTypeManager = new DoctrineFieldTypeManager($this->entityManager);
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new FieldType();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->fieldTypeManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_a_field_type()
    {
        $entity = new FieldType();
        $id = Id::fromInt(1);
        $fieldRepository = Mockery::mock(ObjectRepository::class);
        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(FieldType::class)
            ->andReturn($fieldRepository);
        $fieldRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $field = $this->fieldTypeManager->read($id);

        $this->assertEquals($field, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_throw_an_exception()
    {
        $id = Id::fromInt(20);
        $fieldRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(FieldType::class)
            ->andReturn($fieldRepository);

        $fieldRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(FieldTypeNotFoundException::class);

        $this->fieldTypeManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_field_types()
    {
        $fieldTypeOne = new FieldType();
        $fieldTypeTwo = new FieldType();

        $fieldRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(FieldType::class)
            ->andReturn($fieldRepository);

        $fieldRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$fieldTypeOne, $fieldTypeTwo]);

        $this->assertEquals($this->fieldTypeManager->readAll(), [$fieldTypeOne, $fieldTypeTwo]);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_field_types_and_throw_an_exception()
    {
        $fieldRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(FieldType::class)
            ->andReturn($fieldRepository);

        $fieldRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(FieldTypeNotFoundException::class);

        $this->fieldTypeManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_a_field_type()
    {
        $this->entityManager->shouldReceive('flush')->once();

        $this->fieldTypeManager->update();
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_a_field_type()
    {
        $field = new FieldType();
        $this->entityManager->shouldReceive('remove')->once()->with($field);
        $this->entityManager->shouldReceive('flush')->once();

        $this->fieldTypeManager->delete($field);
    }

    /**
     * @test
     * @covers ::createWithFullyQualifiedClassName
     */
    public function it_should_create_with_fully_qualified_class_name()
    {
        $fullyQualifiedClassName = FullyQualifiedClassName::fromString(
            'There\\Are\\ClassNames\\That\\Are\\Fully\\Qualified'
        );

        $fieldType = new FieldType();
        $fieldType->setType($fullyQualifiedClassName->getClassName());
        $fieldType->setFullyQualifiedClassName((string) $fullyQualifiedClassName);

        $this->entityManager
            ->shouldReceive('persist')
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $createdFieldType = $this->fieldTypeManager
            ->createWithFullyQualifiedClassName($fullyQualifiedClassName);

        $this->assertEquals($createdFieldType, $fieldType);
    }

    /**
     * @test
     * @covers ::readByType
     */
    public function it_should_read_by_type()
    {
        $fieldTypeRepository = Mockery::mock(ObjectRepository::class);

        $type = Type::fromString('TextArea');

        $fieldType = new FieldType();

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(FieldType::class)
            ->andReturn($fieldTypeRepository);

        $fieldTypeRepository
            ->shouldReceive('findOneBy')
            ->with(['type' => (string) $type])
            ->andReturn($fieldType);

        $returnedFieldType = $this->fieldTypeManager->readByType($type);

        $this->assertEquals($fieldType, $returnedFieldType);
    }
}
