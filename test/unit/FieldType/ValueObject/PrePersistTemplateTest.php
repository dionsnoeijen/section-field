<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\ValueObject\PrePersistTemplate
 * @covers ::<private>
 * @covers ::__construct
 */
class PrePersistTemplateTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $template = PrePersistTemplate::create('wheeee');
        $this->assertInstanceOf(PrePersistTemplate::class, $template);
        $this->assertSame((string) $template, 'wheeee');
    }
}
