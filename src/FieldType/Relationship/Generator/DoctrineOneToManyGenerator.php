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

        print_r($fieldConfig);

        /** @var SectionManagerInterface $sectionManager */
        $sectionManager = $options[0]['sectionManager'];

        if ($fieldConfig['field']['kind'] === self::KIND) {

            $handle = Handle::fromString($fieldConfig['field']['handle']);
            /** @var SectionInterface $from */
            if (!empty($fieldConfig['field']['from'])) {
                $handle = Handle::fromString($fieldConfig['field']['from']);
            }
            $from = $sectionManager->readByHandle($handle);

            /** @var SectionInterface $to */
            $to = $sectionManager->readByHandle(Handle::fromString($fieldConfig['field']['to']));

            $fromVersion = $from->getVersion()->toInt() > 1 ? ('_' . $from->getVersion()->toInt()) : '';
            $toVersion = $to->getVersion()->toInt() > 1 ? ('_' . $to->getVersion()->toInt()) : '';

            return Template::create(
                TemplateLoader::load(
                    __DIR__ . '/../GeneratorTemplate/doctrine.onetomany.xml.php', [
                        'toPluralHandle' => Inflector::pluralize($fieldConfig['field']['to']) . $toVersion,
                        'toFullyQualifiedClassName' => $to->getConfig()->getFullyQualifiedClassName(),
                        'fromHandle' => (string) $handle, // Don't version this one, it's mapped to the entity method.
                        'fromPluralHandle' => Inflector::pluralize((string) $handle) . $fromVersion,
                        'toHandle' => $fieldConfig['field']['to'] . $toVersion
                    ]
                )
            );
        }

        return Template::create('');
    }
}
