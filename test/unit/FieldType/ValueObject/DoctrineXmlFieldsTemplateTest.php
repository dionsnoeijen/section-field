<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\ValueObject\DoctrineXmlFieldsTemplate
 * @covers ::<private>
 * @covers ::__construct
 */
class DoctrineXmlFieldsTemplateTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $template = DoctrineXmlFieldsTemplate::create('wheeee');
        $this->assertInstanceOf(DoctrineXmlFieldsTemplate::class, $template);
        $this->assertSame((string) $template, 'wheeee');
    }
}
