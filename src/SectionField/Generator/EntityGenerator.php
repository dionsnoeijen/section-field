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
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\SlugField;

class EntityGenerator extends Generator implements GeneratorInterface
{
    /** @var SectionConfig */
    private $sectionConfig;

    /** @var array */
    private $templates = [
        'use' => [],
        'properties' => [],
        'constructor' => [],
        'methods' => [],
        'prePersist' => [],
        'preUpdate' => []
    ];

    const GENERATE_FOR = 'entity';

    public function generateBySection(
        SectionInterface $section
    ): Writable {

        $this->sectionConfig = $section->getConfig();

        $fields = $this->fieldManager->readByHandles($this->sectionConfig->getFields());
        $fields = $this->addOpposingRelationships($section, $fields);
        $this->generateElements($fields);

        return Writable::create(
            (string) $this->generateEntity(),
            $this->sectionConfig->getNamespace() . '\\Entity\\',
            $this->sectionConfig->getClassName() . '.php'
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

            $this->removeDoubles();
        }
    }

    private function removeDoubles()
    {
        foreach ($this->templates as $item=>&$templates) {
            $templates = array_unique($templates);
        }
    }

    protected function generateSlugFieldGetMethod(SlugField $slugField): string
    {
        return <<<EOT
public function getSlug(): Tardigrades\FieldType\Slug\ValueObject\Slug
{
    return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString(\$this->{$slugField});
}
EOT;
    }

    protected function generateDefaultFieldGetMethod(string $defaultField): string
    {
        return <<<EOT
public function getDefault(): string
{
    return \$this->{$defaultField};
}
EOT;
    }

    private function combine(array $templates): string
    {
        $combined = '';
        foreach ($templates as $template) {
            $combined .= $template;
        }
        return $combined;
    }

    private function insertRenderedTemplates(string $template): string
    {
        foreach ($this->templates as $templateVariable=>$templates) {
            $template = str_replace(
                '{{ ' . $templateVariable . ' }}',
                $this->combine($templates),
                $template
            );
        }

        return $template;
    }

    private function insertSlug(string $template): string
    {
        try {
            if ($this->sectionConfig->getSlugField() !== 'slug') {
                $template = str_replace(
                    '{{ getSlug }}',
                    $this->generateSlugFieldGetMethod($this->sectionConfig->getSlugField()),
                    $template
                );
            }
        } catch (\Exception $exception) {
            $template = str_replace(
                '{{ getSlug }}',
                '',
                $template
            );
            $this->buildMessages[] = 'There is no slug field available, skipping generic method.';
        }

        return $template;
    }

    private function insertDefaultFieldMethod(string $template): string
    {
        $template = str_replace(
            '{{ getDefault }}',
            $this->generateDefaultFieldGetMethod($this->sectionConfig->getDefault()),
            $template
        );

        return $template;
    }

    private function insertSection(string $template): string
    {
        $template = str_replace(
            '{{ section }}',
            $this->sectionConfig->getClassName(),
            $template
        );

        return $template;
    }

    private function insertNamespace(string $template): string
    {
        $template = str_replace(
            '{{ namespace }}',
            (string) $this->sectionConfig->getNamespace() . '\\Entity',
            $template
        );

        return $template;
    }

    private function insertValidationMetadata(string $template): string
    {
        $generatorConfig = $this->sectionConfig->getGeneratorConfig()->toArray();
        $metadata = '';
        foreach ($generatorConfig['entity'] as $handle => $options) {

            $field = $this->fieldManager->readByHandle(Handle::fromString($handle));

            foreach ($options as $assertion => $assertionOptions) {
                try {
                    $asString = (string)Template::create(
                        (string)TemplateLoader::load(
                            $field->getFieldType()->getInstance()->directory() .
                            '/GeneratorTemplate/entity.validator-metadata.php.template'
                        )
                    );
                    $asString = str_replace(
                        '{{ propertyName }}',
                        $field->getHandle(),
                        $asString
                    );
                    $asString = str_replace(
                        '{{ assertion }}',
                        $assertion,
                        $asString
                    );
                    $arguments = '';
                    if (is_array($assertionOptions)) {
                        foreach ($assertionOptions as $optionKey => $optionValue) {
                            $arguments .= "'{$optionKey}' => '{$optionValue}',";
                        }
                        if (!empty($arguments)) {
                            $arguments = rtrim($arguments, ',');
                            $arguments = "[{$arguments}]";
                        }
                    }
                    $asString = str_replace(
                        '{{ assertionOptions }}',
                        $arguments,
                        $asString
                    );
                    if (strpos($template, $asString) === false) {
                        // Add to metadata
                        $metadata .= $asString;
                    }
                } catch (\Exception $exception) {
                    $this->buildMessages[] = $exception->getMessage();
                }
            }
        }

        // Insert
        $template = str_replace(
            '{{ validatorMetadataSectionPhase }}',
            $metadata,
            $template
        );

        return $template;
    }

    private function generateEntity(): Template
    {
        $template = TemplateLoader::load(__DIR__ . '/GeneratorTemplate/entity.php.template');

        $template = $this->insertRenderedTemplates($template);
        $template = $this->insertSlug($template);
        $template = $this->insertDefaultFieldMethod($template);
        $template = $this->insertSection($template);
        $template = $this->insertNamespace($template);
        $template = $this->insertValidationMetadata($template);

        return Template::create(PhpFormatter::format($template));
    }
}
