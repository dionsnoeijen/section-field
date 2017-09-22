<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Assert\Assertion;
use ReflectionClass;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\Generator\Writer\Writable;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class DoctrineConfigGenerator extends Generator implements GeneratorInterface
{
    /** @var array */
    private $templates = [
        'fields' => [],
        'manyToOne' => [],
        'oneToMany' => [],
        'oneToOne' => [],
        'manyToMany' => []
    ];

    /** @var SectionInterface */
    private $section;

    /** @var SectionConfig */
    private $sectionConfig;

    const GENERATE_FOR = 'doctrine';

    public function generateBySection(
        SectionInterface $section
    ): Writable {

        $this->section = $section;
        $this->sectionConfig = $section->getConfig();

        $fields = $this->fieldManager->readByHandles($this->sectionConfig->getFields());
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
        /** @var FieldInterface $field */
        foreach ($fields as $field) {

            $yml = $field->getFieldType()->getInstance()->directory() . '/config/config.yml';
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
             * @var \Tardigrades\FieldType\Generator\GeneratorInterface $generator
             */
            foreach ($parsed['generator'][self::GENERATE_FOR] as $item=>$generator) {
                if (!key_exists($item, $this->templates)) {
                    $this->templates[$item] = [];
                }
                if (class_exists($generator)) {
                    $interfaces = class_implements($generator);
                } else {
                    $this->buildMessages[] = 'Generators ' . get_class($generator) . ': Generators not found.';
                    break;
                }
                if (key($interfaces) === \Tardigrades\FieldType\Generator\GeneratorInterface::class) {
                    try {
                        $reflector = new ReflectionClass($generator);
                        $method = $reflector->getMethod('generate');
                        $options = null;
                        if (isset($method->getParameters()[1])) {
                            $options = [
                                'sectionManager' => $this->sectionManager,
                                'sectionConfig' => $this->sectionConfig
                            ];
                        }
                        $this->templates[$item][] = $generator::generate($field, $options);
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
            (string) $this->sectionConfig->getFullyQualifiedClassName(),
            $asString
        );

        $tableVersion = $this->section->getVersion()->toInt() > 1 ?
            ('_' . $this->section->getVersion()->toInt()) : '';

        $asString = str_replace(
            '{{ handle }}',
            (string) $this->sectionConfig->getHandle() . $tableVersion,
            $asString
        );

        return Template::create(XmlFormatter::format($asString));
    }
}
