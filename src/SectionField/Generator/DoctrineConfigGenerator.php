<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Assert\Assertion;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\Generator\Writer\Writable;
use Tardigrades\SectionField\SectionFieldInterface\Generator as GeneratorInterface;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class DoctrineConfigGenerator extends Generator implements GeneratorInterface
{
    /** @var array */
    private $buildMessages = [];

    /** @var array */
    private $templates = [
        'fields' => []
    ];

    /** @var SectionConfig */
    private $sectionConfig;

    const GENERATE_FOR = 'doctrine';

    public function generateBySection(
        Section $section
    ): Writable {
        $this->sectionConfig = $section->getConfig();

        $fields = $this->fieldManager->readFieldsByHandles($this->sectionConfig->getFields());
        $fields = $this->addOpposingRelationships($section, $fields);

        $this->generateElements($fields);

        return Writable::create(
            (string) $this->generateXml(),
            $this->sectionConfig->getNamespace() . '\\config\\xml\\',
            str_replace('\\', '.', $this->sectionConfig->getNamespace()) .
            '.Entity.' . ucfirst((string) $this->sectionConfig->getHandle()) . '.dcm.xml'
        );
    }

    private function generateElements(array $fields): void
    {
        /** @var Field $field */
        foreach ($fields as $field) {

            $yml = FullyQualifiedClassNameConverter::toDir(
                $field->getFieldType()->getFullyQualifiedClassName()
            ) . '/config/config.yml';

            $parsed = Yaml::parse(\file_get_contents($yml));

            try {
                $label = !empty($field->getFieldTranslations()[0]) ?
                    $field->getFieldTranslations()[0]->getLabel() :
                    'Opposing field';
                Assertion::keyExists(
                    $parsed,
                    'generator',
                    'No generator defined for ' .
                    $label . 'type: ' . $field->getFieldType()->getFullyQualifiedClassName()
                );

                Assertion::keyExists(
                    $parsed['generator'],
                    self::GENERATE_FOR,
                    'Nothing to do for this generator: ' . self::GENERATE_FOR
                );
            } catch (\Exception $exception) {
                $this->buildMessages[] = $exception->getMessage();
            }

            /**
             * @var string $item
             * @var \Tardigrades\FieldType\FieldTypeInterface\Generator $generator
             */
            foreach ($parsed['generator'][self::GENERATE_FOR] as $item=>$generator) {
                if (!key_exists($item, $this->templates)) {
                    $this->templates[$item] = [];
                }
                if (class_exists($generator)) {
                    $interfaces = class_implements($generator);
                } else {
                    $this->buildMessages[] = 'Generators ' . $generator . ': Generators not found.';
                    break;
                }
                if (key($interfaces) === \Tardigrades\FieldType\FieldTypeInterface\Generator::class) {
                    try {
                        // @todo, the passing of manager will not be sufficient or efficient
                        // Much better would be to define generators as services.
                        $this->templates[$item][] = $generator::generate($field, $this->sectionManager);
                    } catch (\Exception $exception) {
                        $this->buildMessages[] = $exception->getMessage();
                    }
                }
            }
        }
    }

    private function combine(array $templates): string
    {
        $combined = '';
        foreach ($templates as $template) {
            $combined .= $template;
        }
        return $combined;
    }

    private function generateXml(): Template
    {
        $asString = (string) TemplateLoader::load(__DIR__ . '/GeneratorTemplate/doctrine.config.xml.template');

        foreach ($this->templates as $templateVariable=>$templates) {
            $asString = str_replace(
                '{{ ' . $templateVariable . ' }}',
                $this->combine($templates),
                $asString
            );
        }

        $asString = str_replace(
            '{{ fullyQualifiedClassName }}',
            (string) $this->sectionConfig->getNamespace() . '\\Entity\\' . ucfirst(
                StringConverter::toCamelCase((string) $this->sectionConfig->getName())
            ),
            $asString
        );
        $asString = str_replace(
            '{{ handle }}',
            (string) $this->sectionConfig->getHandle(),
            $asString
        );

        return Template::create($asString);
    }
}
