<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\ValueObject\DoctrineXmlFieldsTemplate;
use Tardigrades\SectionField\Generator\Loader\CustomGeneratorLoader;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\SectionFieldInterface\FieldManager;
use Tardigrades\SectionField\SectionFieldInterface\Generator;
use Tardigrades\SectionField\ValueObject\DoctrineXmlConfigTemplate;
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

    protected function generateFields(array $fields): DoctrineXmlFieldsTemplate
    {
        $xmlFields = '';

        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $customGenerator = CustomGeneratorLoader::load($field);

            } catch (\Exception $exception) {
                $this->buildMessages[] = $exception->getMessage();
            }
        }

        return DoctrineXmlFieldsTemplate::create($xmlFields);
    }

    protected function generateXmlBase(SectionConfig $sectionConfig, array $fields): string
    {
        $template = DoctrineXmlConfigTemplate::create(
            TemplateLoader::load(__DIR__ . '/GeneratorTemplate/doctrine.config.xml.template')
        );

        $asString = (string) $template;

        $asString = str_replace(
            '{{ fields }}',
            $this->generateFields($fields),
            $asString
        );

        print_r($asString);

        return $asString;
    }
}
