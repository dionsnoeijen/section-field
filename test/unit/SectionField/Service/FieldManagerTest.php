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
use Tardigrades\SectionField\SectionFieldInterface\FieldTypeManager as FieldTypeManagerInterface;
use Tardigrades\SectionField\SectionFieldInterface\LanguageManager as LanguageManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Id;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\FieldManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class FieldManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FieldTypeManagerInterface
     */
    private $fieldTypeManager;

    /**
     * @var LanguageManagerInterface
     */
    private $languageManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->fieldTypeManager = Mockery::mock(FieldTypeManagerInterface::class);
        $this->languageManager = Mockery::mock(LanguageManagerInterface::class);
        $this->fieldManager = new FieldManager(
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
        $id = Id::create(1);
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
        $id = Id::create(20);
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
        $field = new Field();

        $this->entityManager->shouldReceive('persist')->once()->with($field);
        $this->entityManager->shouldReceive('flush')->once();

        $receive = $this->fieldManager->update($field);

        $this->assertSame($receive, $field);
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
        $fieldConfig = FieldConfig::create([
            'field' => [
                'name' => [
                    'en_EN' => 'This is my name',
                    'nl_NL' => 'Dit is mijn naam'
                ],
                'type' => 'TextField'
            ]
        ]);

        $field = $this->givenAField();

        $this->fieldTypeManager
            ->shouldReceive('readByType')
            ->once();

        $this->languageManager
            ->shouldReceive('readByI18n')
            ->twice()
            ->andReturn(
                (new Language())->setI18n('nl_NL'),
                (new Language())->setI18n('en_EN')
            );

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
        $fieldConfig = FieldConfig::create([
            'field' => [
                'name' => [
                    'en_EN' => 'This is my other name',
                    'nl_NL' => 'Dit is mijn andere naam'
                ],
                'type' => 'TextArea'
            ]
        ]);

        $field = $this->givenAField();

        $this->fieldTypeManager
            ->shouldReceive('readByType')
            ->once();

        $this->languageManager
            ->shouldReceive('readByI18n')
            ->twice()
            ->andReturn(
                (new Language())->setI18n('nl_NL'),
                (new Language())->setI18n('en_EN')
            );

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $returnedField = $this->fieldManager->updateByConfig($fieldConfig, $field);

        $this->assertSame($field, $returnedField);
        $this->assertEquals($returnedField->getHandle(), $field->getHandle());
    }

    /**
     * @test
     * @covers ::createByConfig
     */
    public function it_should_receive_a_no_field_name_defined_exception()
    {
        $fieldConfig = FieldConfig::create([
            'field' => [
                'name' => 'This should be an array',
                'type' => 'TextArea'
            ]
        ]);

        $field = $this->givenAField();
        $this->fieldTypeManager
            ->shouldReceive('readByType')
            ->once();

        $this->expectException(NoFieldNameDefinedException::class);

        $returnedField = $this->fieldManager->createByConfig($fieldConfig, $field);
    }

    /**
     * @test
     * @covers ::readFieldsByHandles
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
            ->andReturn([]);

        $this->fieldManager->readFieldsByHandles($handles);
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

