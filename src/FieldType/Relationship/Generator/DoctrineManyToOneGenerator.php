<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Tardigrades\Entity\FieldInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\Generator\GeneratorInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\Handle;

class DoctrineManyToOneGenerator implements GeneratorInterface
{
    const KIND = 'many-to-one';

    public static function generate(FieldInterface $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        /** @var SectionManager $sectionManager */
        $sectionManager = $options[0]['sectionManager'];

        if ($fieldConfig['field']['kind'] === self::KIND) {

            /** @var SectionInterface $target */
            $target = $sectionManager->readByHandle(Handle::fromString($fieldConfig['field']['to']));

            return Template::create(
                TemplateLoader::load(
                    __DIR__ . '/../GeneratorTemplate/doctrine.manytoone.xml.php', [
                        'thatHandle' => $fieldConfig['field']['to'],
                        'thatFullyQualifiedClassName' => $target->getConfig()->getFullyQualifiedClassName()
                    ]
                )
            );
        }

        return Template::create('');
    }
}
