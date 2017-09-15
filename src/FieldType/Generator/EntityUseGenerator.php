<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityUseGenerator implements GeneratorInterface
{
    public static function generate(FieldInterface $field): Template
    {
        return Template::create(
            (string) TemplateLoader::load(
               $field->getFieldType()->getInstance()->directory() .
                '/GeneratorTemplate/entity.use.php.template'
            )
        );
    }
}
