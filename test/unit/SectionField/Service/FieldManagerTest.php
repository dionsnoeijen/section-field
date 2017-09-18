<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\AbstractQuery;
use Mockery;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldTranslation;
use Tardigrades\Entity\FieldType;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Id;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DoctrineFieldManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class FieldManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var DoctrineFieldManager */
    private $fieldManager;

    /** @var EntityManagerInterface|Mockery\MockInterface */
    private $entityManager;

    /** @var FieldTypeManagerInterface|Mockery\MockInterface */
    private $fieldTypeManager;

    /** @var LanguageManagerInterface */
    private $languageManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->fieldTypeManager = Mockery::mock(FieldTypeManagerInterface::class);
        $this->languageManager = Mockery::mock(LanguageManagerInterface::class);
        $this->fieldManager = new DoctrineFieldManager(
            $this->entityManager,
            $this->fieldTypeManager,
            $this->languageManager
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new Field();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->fieldManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_a_field()
    {
        $entity = new Field();
        $id = Id::fromInt(1);
        $fieldRepository = Mockery::mock(ObjectRepository::class);
        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Field::class)
            ->andReturn($fieldRepository);
        $fieldRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $field = $this->fieldManager->read($id);

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
            ->with(Field::class)
            ->andReturn($fieldRepository);

        $fieldRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(FieldNotFoundException::class);

        $this->fieldManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_fields()
    {
        $fieldOne = new Field();
        $fieldTwo = new Field();

        $fieldRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Field::class)
            ->andReturn($fieldRepository);

        $fieldRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$fieldOne, $fieldTwo]);

        $this->assertEquals($this->fieldManager->readAll(), [$fieldOne, $fieldTwo]);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_fields_and_throw_an_exception()
    {
        $fieldRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Field::class)
            ->andReturn($fieldRepository);

        $fieldRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(FieldNotFoundException::class);

        $this->fieldManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_a_field()
    {
        $this->entityManager->shouldReceive('flush')->once();

        $this->fieldManager->update();
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_a_field()
    {
        $field = new Field();
        $this->entityManager->shouldReceive('remove')->once()->with($field);
        $this->entityManager->shouldReceive('flush')->once();

        $this->fieldManager->delete($field);
    }

    /**
     * @test
     * @covers ::createByConfig
     */
    public function it_should_create_by_config()
    {
        $fieldConfig = FieldConfig::fromArray([
            'field' => [
                'name' => [
                    ['en_EN' => 'This is my name'],
                    ['nl_NL' => 'Dit is mijn naam']
                ],
                'handle' => 'thisIsMyName',
                'label' => [
                    ['en_EN' => 'I also have a label'],
                    ['nl_NL' => 'Ik heb ook een label']
                ],
                'type' => 'TextArea'
            ]
        ]);

        $field = $this->givenAField();

        $this->fieldTypeManager
            ->shouldReceive('readByType')
            ->once();

        $this->languageManager
            ->shouldReceive('readByI18ns')
            ->once();

        $this->entityManager
            ->shouldReceive('persist')
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $returnedField = $this->fieldManager
            ->createByConfig($fieldConfig);

        $this->assertEquals(
            $returnedField->getHandle(),
            $field->getHandle()
        );
    }

    /**
     * @test
     * @covers ::updateByConfig
     */
    public function it_should_update_by_config()
    {
        $fieldConfig = FieldConfig::fromArray([
            'field' => [
                'name' => [
                    ['en_EN' => 'This is my other name'],
                    ['nl_NL' => 'Dit is mijn andere naam']
                ],
                'handle' => 'thisIsMyOtherName',
                'label' => [
                    ['en_EN' => 'I also have al label'],
                    ['nl_NL' => 'Ik heb ook een label']
                ],
                'type' => 'TextArea'
            ]
        ]);

        $field = $this->givenAField();

        $this->fieldTypeManager
            ->shouldReceive('readByType')
            ->once();

        $this->languageManager
            ->shouldReceive('readByI18ns')
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $returnedField = $this->fieldManager->updateByConfig($fieldConfig, $field);

        $this->assertSame($field, $returnedField);
        $this->assertEquals($returnedField->getHandle(), $field->getHandle());
    }

    /**
     * @test
     * @covers ::readByHandles
     */
    public function it_should_read_fields_by_handles()
    {
        $handles = [
            'fieldHandle',
            'anotherFieldHandle'
        ];

        $query = Mockery::mock(AbstractQuery::class);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->andReturn($query);

        $query->shouldReceive('getResult')
            ->once()
            ->andReturn([
                new Field(),
                new Field()
            ]);

        $fields = $this->fieldManager->readByHandles($handles);

        $this->assertEquals(count($fields), 2);
    }

    /**
     * @test
     * @covers ::readByHandles
     */
    public function it_make_exception_when_read_fields_by_handles()
    {
        $handles = [
            'fieldHandle',
            'anotherFieldHandle'
        ];

        $query = Mockery::mock(AbstractQuery::class);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->andReturn($query);

        $query->shouldReceive('getResult')
            ->once()
            ->andReturn([
            ]);

        $this->expectException(FieldNotFoundException::class);

        $this->fieldManager->readByHandles($handles);
    }

    private function givenAField()
    {
        $field = new Field();
        $fieldType = new FieldType();

        $field->setFieldType($fieldType);
        $field->setHandle('thisIsMyName');
        $field->addFieldTranslation(
            (new FieldTranslation())
                ->setName('I have a name')
                ->setLanguage(
                    (new Language())
                        ->setI18n('en_EN')
                )
        );

        return $field;
    }
}
