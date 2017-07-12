<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DoctrineSectionManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class SectionManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var DoctrineSectionManager
     */
    private $sectionManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DoctrineFieldManager
     */
    private $fieldManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->fieldManager = Mockery::mock(FieldManager::class);
        $this->sectionManager = new DoctrineSectionManager(
            $this->entityManager,
            $this->fieldManager
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new Section();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->sectionManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_a_section()
    {
        $entity = new Section();
        $id = Id::create(1);
        $fieldRepository = Mockery::mock(ObjectRepository::class);
        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($fieldRepository);
        $fieldRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $section = $this->sectionManager->read($id);

        $this->assertEquals($section, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_throw_an_exception()
    {
        $id = Id::create(20);
        $sectionRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($sectionRepository);

        $sectionRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(SectionNotFoundException::class);

        $this->sectionManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_sections()
    {
        $sectionOne = new Section();
        $sectionTwo = new Section();

        $sectionRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($sectionRepository);

        $sectionRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$sectionOne, $sectionTwo]);

        $this->assertEquals($this->sectionManager->readAll(), [$sectionOne, $sectionTwo]);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_field_types_and_throw_an_exception()
    {
        $sectionRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($sectionRepository);

        $sectionRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(SectionNotFoundException::class);

        $this->sectionManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_a_section()
    {
        $section = new Section();

        $this->entityManager->shouldReceive('persist')->once()->with($section);
        $this->entityManager->shouldReceive('flush')->once();

        $receive = $this->sectionManager->update($section);

        $this->assertSame($receive, $section);
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_a_section()
    {
        $section = new Section();
        $this->entityManager->shouldReceive('remove')->once()->with($section);
        $this->entityManager->shouldReceive('flush')->once();

        $this->sectionManager->delete($section);
    }

    /**
     * @test
     * @covers ::createByConfig
     */
    public function it_should_create_by_config()
    {
        $sectionConfig = SectionConfig::create([
            'section' => [
                'name' => 'Super Section',
                'fields' => [
                    'title',
                    'body',
                    'created'
                ],
                'slug' => ['title'],
                'default' => 'title'
            ]
        ]);

        $section = $this->givenASection();

        $this->fieldManager
            ->shouldReceive('readFieldsByHandles')
            ->once();

        $this->entityManager
            ->shouldReceive('persist')
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $createdSection = $this->sectionManager->createByConfig($sectionConfig);

        $this->assertEquals($createdSection->getName(), $section->getName());
        $this->assertEquals($createdSection->getHandle(), $section->getHandle());
    }

    /**
     * @test
     * @covers ::updateByConfig
     */
    public function it_should_update_by_config()
    {
        $sectionConfig = SectionConfig::create([
            'section' => [
                'name' => 'Super Section',
                'fields' => [
                    'title',
                    'body',
                    'created'
                ],
                'slug' => ['title'],
                'default' => 'title'
            ]
        ]);

        $section = $this->givenASection();

        $this->fieldManager
            ->shouldReceive('readFieldsByHandles')
            ->once();

        $this->entityManager
            ->shouldReceive('persist')
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $createdSection = $this->sectionManager->updateByConfig($sectionConfig, $section);

        $this->assertSame($section, $createdSection);
        $this->assertEquals($createdSection->getName(), $section->getName());
        $this->assertEquals($createdSection->getHandle(), $section->getHandle());
    }

    private function givenASection()
    {
        $section = new Section();

        $section->setName('Super Section');
        $section->setHandle('superSection');

        return $section;
    }
}
