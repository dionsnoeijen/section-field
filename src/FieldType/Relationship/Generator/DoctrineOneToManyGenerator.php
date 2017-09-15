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

class DoctrineOneToManyGenerator implements Generator
{
    const KIND = 'one-to-many';

    public static function generate(Field $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        /** @var SectionManager $sectionManager */
        $sectionManager = $options[0]['sectionManager'];

        if ($fieldConfig['field']['kind'] === self::KIND) {

            /** @var Section $target */
            $target = $sectionManager->readByHandle(Handle::fromString($fieldConfig['field']['to']));

            return Template::create(
                TemplateLoader::load(
                    __DIR__ . '/../GeneratorTemplate/doctrine.onetomany.xml.php', [
                        'thatPluralHandle' => Inflector::pluralize($fieldConfig['field']['to']),
                        'thatFullyQualifiedClassName' => $target->getConfig()->getFullyQualifiedClassName(),
                        'thisHandle' => $fieldConfig['field']['handle'],
                        'thisPluralHandle' => Inflector::pluralize($fieldConfig['field']['handle']),
                        'thatHandle' => $fieldConfig['field']['to']
                    ]
                )
            );
        }

        return Template::create('');
    }
}
