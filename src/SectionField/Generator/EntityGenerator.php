<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Assert\Assertion;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\Generator\Writer\Writable;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\Generator;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\SlugField;

class EntityGenerator implements Generator
{
    /** @var FieldManager */
    private $fieldManager;

    /** @var array */
    private $buildMessages = [];

    /** @var array */
    private $templates = [
        'properties' => [],
        'constructor' => [],
        'methods' => [],
        'prePersist' => [],
        'preUpdate' => []
    ];

    /** @var SectionConfig */
    private $sectionConfig;

    const GENERATE_FOR = 'entity';

    public function __construct(
        FieldManager $fieldManager
    ) {
        $this->fieldManager = $fieldManager;
    }

    public function generateBySection(
        Section $section
    ): Writable {
        $this->sectionConfig = $section->getConfig();

        $fields = $this->fieldManager->readFieldsByHandles($this->sectionConfig->getFields());

        $this->generateElements($fields);

        return Writable::create(
            (string) $this->generateEntity(),
            $this->sectionConfig->getNamespace() . '\\Entity\\',
            $this->sectionConfig->getClassName() . '.php'
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

            Assertion::keyExists(
                $parsed,
                'generator',
                'No generator defined for ' .
                    $field->getFieldTranslations()[0]->getLabel() .
                    'type: ' . $field->getFieldType()->getFullyQualifiedClassName()
            );

            Assertion::keyExists(
                $parsed['generator'],
                self::GENERATE_FOR,
                'Nothing to do for this generator: ' . self::GENERATE_FOR
            );

            /**
             * @var string $item
             * @var \Tardigrades\FieldType\FieldTypeInterface\Generator $generator
             */
            foreach ($parsed['generator'][self::GENERATE_FOR] as $item=>$generator) {
                if (!key_exists($item, $this->templates)) {
                    $this->templates[$item] = [];
                }

                $interfaces = class_implements($generator);
                if (key($interfaces) === \Tardigrades\FieldType\FieldTypeInterface\Generator::class)
                {
                    try {
                        $this->templates[$item][] = $generator::generate($field);
                    } catch (\Exception $exception) {
                        $this->buildMessages[] = $exception->getMessage();
                    }
                }
            }
        }
    }

    public function getBuildMessages(): array
    {
        return $this->buildMessages;
    }

    protected function generateSlugFieldGetMethod(SlugField $slugField)
    {
        return <<<EOT
public function getSlug(): Tardigrades\FieldType\Slug\ValueObject\Slug
{
    return Tardigrades\FieldType\Slug\ValueObject\Slug::fromString(\$this->{$slugField});
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

    private function generateEntity(): Template
    {
        $asString = TemplateLoader::load(__DIR__ . '/GeneratorTemplate/entity.php.template');

        foreach ($this->templates as $templateVariable=>$templates) {
            $asString = str_replace(
                '{{ ' . $templateVariable . ' }}',
                $this->combine($templates),
                $asString
            );
        }

        try {
            if ($this->sectionConfig->getSlugField() !== 'slug') {
                $asString = str_replace(
                    '{{ getSlug }}',
                    $this->generateSlugFieldGetMethod($this->sectionConfig->getSlugField()),
                    $asString
                );
            }
        } catch (\Exception $exception) {
            $asString = str_replace(
                '{{ getSlug }}',
                '',
                $asString
            );
            $this->buildMessages[] = 'There is no slug field available, skipping generic method.';
        }

        $asString = str_replace(
            '{{ section }}',
            $this->sectionConfig->getClassName(),
            $asString
        );
        $asString = str_replace(
            '{{ namespace }}',
            (string) $this->sectionConfig->getNamespace() . '\\Entity',
            $asString
        );

        return Template::create(PhpFormatter::format($asString));
    }
}
