<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Doctrine\Common\Util\Inflector;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\Generator\GeneratorInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\Handle;

class DoctrineOneToManyGenerator implements GeneratorInterface
{
    const KIND = 'one-to-many';

    public static function generate(FieldInterface $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        /** @var SectionManagerInterface $sectionManager */
        $sectionManager = $options[0]['sectionManager'];

        if ($fieldConfig['field']['kind'] === self::KIND) {

            /** @var SectionInterface $from */
            $from = $sectionManager->readByHandle(Handle::fromString($fieldConfig['field']['from']));

            /** @var SectionInterface $to */
            $to = $sectionManager->readByHandle(Handle::fromString($fieldConfig['field']['to']));

            return Template::create(
                TemplateLoader::load(
                    __DIR__ . '/../GeneratorTemplate/doctrine.onetomany.xml.php', [
                        'toPluralHandle' => Inflector::pluralize($fieldConfig['field']['to']) . '_' . (string) $to->getVersion(),
                        'toFullyQualifiedClassName' => $to->getConfig()->getFullyQualifiedClassName(),
                        'fromHandle' => $fieldConfig['field']['handle'] . '_' . (string) $from->getVersion(),
                        'fromPluralHandle' => Inflector::pluralize($fieldConfig['field']['handle']) . '_' . (string) $from->getVersion(),
                        'toHandle' => $fieldConfig['field']['to'] . '_' . (string) $to->getVersion()
                    ]
                )
            );
        }

        return Template::create('');
    }
}
