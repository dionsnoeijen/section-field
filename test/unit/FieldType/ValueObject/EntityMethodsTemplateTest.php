<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\ValueObject\EntityMethodsTemplate
 * @covers ::<private>
 * @covers ::__construct
 */
class EntityMethodsTemplateTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $template = EntityMethodsTemplate::create('wheeee');
        $this->assertInstanceOf(EntityMethodsTemplate::class, $template);
        $this->assertSame((string) $template, 'wheeee');
    }
}
