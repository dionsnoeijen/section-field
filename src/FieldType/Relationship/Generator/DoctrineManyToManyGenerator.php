<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Doctrine\Common\Util\Inflector;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * Class DoctrineManyToManyGenerator
 *
 * @todo: We have an automatic inverse relationship detector in
 * the generators. What we have to take care of is that the opposing
 * relationship for a many to many field in case of a unidirectional
 * opposing side get's the correct opposing field added. With type
 * bidirectional.
 *
 * @package Tardigrades\FieldType\Relationship\Generator
 */
class DoctrineManyToManyGenerator implements Generator
{
    const KIND = 'many-to-many';

    public static function generate(Field $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        /** @var SectionManager $sectionManager */
        $sectionManager = $options[0]['sectionManager'];

        /** @var SectionConfig $sectionConfig */
        $sectionConfig = $options[0]['sectionConfig'];

        if ($fieldConfig['field']['kind'] === self::KIND) {

            /** @var Section $target */
            $target = $sectionManager->readByHandle(Handle::fromString($fieldConfig['field']['to']));

            return Template::create(
                TemplateLoader::load(
                    __DIR__ . '/../GeneratorTemplate/doctrine.manytomany.xml.php', [
                        'type' => $fieldConfig['field']['relationship-type'],
                        'thatPluralHandle' => Inflector::pluralize(
                            $fieldConfig['field']['to']
                        ),
                        'thatFullyQualifiedClassName' => $target
                            ->getConfig()
                            ->getFullyQualifiedClassName(),
                        'thisHandle' => $fieldConfig['field']['from'],
                        'thisPluralHandle' => Inflector::pluralize(
                            $fieldConfig['field']['from']
                        ),
                        'thisFullyQualifiedClassName' => $sectionConfig
                            ->getFullyQualifiedClassName(),
                        'thatHandle' => $fieldConfig['field']['to']
                    ]
                )
            );
        }

        return Template::create('');
    }
}
