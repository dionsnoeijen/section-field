<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\NoCustomDoctrineConfigFieldMethodDefinedException;
use Tardigrades\FieldType\NoCustomGeneratorDefinedException;
use Tardigrades\FieldType\ValueObject\DoctrineXmlFieldsTemplate;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\Helper\StringConverter;
use Tardigrades\SectionField\Generator\Loader\CustomGeneratorLoader;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\Generator;
use Tardigrades\SectionField\ValueObject\DoctrineXmlConfigTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class DoctrineConfigGenerator implements Generator
{
    /** @var FieldManager */
    private $fieldManager;

    /** @var array */
    private $buildMessages = [];

    public function __construct(
        FieldManager $fieldManager
    ) {
        $this->fieldManager = $fieldManager;
    }

    public function generateBySection(Section $section): void
    {
        $sectionConfig = $section->getConfig();

        $fields = $this->fieldManager->readFieldsByHandles($sectionConfig->getFields());

        $this->generateXmlBase($sectionConfig, $fields);
    }

    public function getBuildMessages(): array
    {
        return $this->buildMessages;
    }

    protected function generateFields(array $fields): string
    {
        $xmlFields = '';
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $customGenerator = CustomGeneratorLoader::load($field);
                if (!method_exists($customGenerator, 'renderField')) {
                    throw new NoCustomDoctrineConfigFieldMethodDefinedException();
                }
                $customGenerator->renderField($field->getConfig());
            } catch (\Exception $exception) {
                if ($exception instanceof NoCustomGeneratorDefinedException ||
                    $exception instanceof NoCustomDoctrineConfigFieldMethodDefinedException
                ) {
                    try {
                        $xmlFields .= (string) $this->renderXmlFields(
                            $field->getConfig(),
                            $field
                                ->getFieldType()
                                ->getFullyQualifiedClassName()
                        );
                    } catch (\Exception $exception) {
                        $this->buildMessages[] = $exception->getMessage();
                    }
                }
            }
        }

        return $xmlFields;
    }

    protected function generateXmlBase(SectionConfig $sectionConfig, array $fields): string
    {
        $asString = (string) DoctrineXmlConfigTemplate::create(
            TemplateLoader::load(__DIR__ . '/GeneratorTemplate/doctrine.config.xml.template')
        );

        $asString = str_replace(
            '{{ fields }}',
            $this->generateFields($fields),
            $asString
        );
        $asString = str_replace(
            '{{ fullyQualifiedClassName }}',
            (string) $sectionConfig->getNamespace() . '\\Entity\\' . ucfirst(
                StringConverter::toCamelCase((string) $sectionConfig->getName())
            ),
            $asString
        );
        $asString = str_replace(
            '{{ handle }}',
            (string) $sectionConfig->getHandle(),
            $asString
        );

        print_r($asString);

        return $asString;
    }

    private function renderXmlFields(
        FieldConfig $config,
        FullyQualifiedClassName $fullyQualifiedClassName
    ): DoctrineXmlFieldsTemplate {

        $asString = (string) DoctrineXmlFieldsTemplate::create(
            TemplateLoader::load(FullyQualifiedClassNameConverter::toDir($fullyQualifiedClassName) . '/GeneratorTemplate/doctrine.config.xml.template')
        );

        $asString = str_replace(
            '{{ handle }}',
            $config->getHandle(),
            $asString
        );

        return DoctrineXmlFieldsTemplate::create($asString);
    }
}
