<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\ValueObject\Template
 * @covers ::<private>
 * @covers ::__construct
 */
class TemplateTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $template = Template::create('wheeee');
        $this->assertInstanceOf(Template::class, $template);
        $this->assertSame((string) $template, 'wheeee');
    }
}
