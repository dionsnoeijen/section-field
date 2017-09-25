<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Forms;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldTypeInterface;
use Tardigrades\FieldType\Slug\ValueObject\Slug;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Form\FormInterface as SectionFormInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\JitRelationship;
use Tardigrades\SectionField\Service\ReadOptions;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\SectionFormOptions;

class Form implements SectionFormInterface
{
    /** @var SectionManagerInterface */
    private $sectionManager;

    /** @var FormFactory */
    private $formFactory;

    /** @var ReadSectionInterface */
    private $readSection;

    public function __construct(
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection,
        FormFactory $formFactory = null
    ) {
        $this->sectionManager = $sectionManager;
        $this->readSection = $readSection;
        $this->formFactory = $formFactory;
    }

    /**
     * This method generates a form based on the section config.
     * The for handle contains the section handle, or the FQCN of the section entity.
     * By pasing along SectionFormOptions this method can determine of the form
     * is meanth to update or create new data.
     *
     * @param string $forHandle
     * @param SectionFormOptions|null $sectionFormOptions
     * @param bool $csrfProtection
     * @return FormInterface
     */
    public function buildFormForSection(
        string $forHandle,
        SectionFormOptions $sectionFormOptions = null,
        bool $csrfProtection = true
    ): FormInterface {

        $sectionConfig = $this->getSectionConfig($forHandle);
        $section = $this->getSection($sectionConfig->getFullyQualifiedClassName());

        // If we have a slug, it means we are updating something.
        // Prep so we can get the correct $sectionEntity
        $slug = null;
        if ($sectionFormOptions !== null) {
            try {
                $slug = $sectionFormOptions->getSlug();
            } catch (\Exception $exception) {
                $slug = null;
            }
        }

        // If we hava an id, it means we are updating something.
        // Prep so we can get the correct $sectionEntity
        $id = null;
        if ($sectionFormOptions !== null) {
            try {
                $id = $sectionFormOptions->getId();
            } catch (\Exception $exception) {
                $id = null;
            }
        }

        $sectionEntity = $this->getSectionEntity(
            $sectionConfig->getFullyQualifiedClassName(),
            $section,
            $slug,
            $id
        );
        $factory = $this->getFormFactory();

        $form = $factory
            ->createBuilder(
                FormType::class,
                $sectionEntity,
                [
                    'method' => 'POST',
                    'attr' => [
                        'novalidate' => 'novalidate'
                    ],
                    'csrf_protection' => $csrfProtection,
                    'csrf_field_name' => 'token',
                    'csrf_token_id'   => 'tardigrades',
                    'allow_extra_fields' => true // This is required for jit relationships.
                ]
            );

        /** @var FieldInterface $field */
        foreach ($section->getFields() as $field) {
            $fieldTypeFullyQualifiedClassName = (string) $field
                ->getFieldType()
                ->getFullyQualifiedClassName();

            /** @var FieldTypeInterface $fieldType */
            $fieldType = new $fieldTypeFullyQualifiedClassName;
            $fieldType->setConfig($field->getConfig());
            $fieldType->addToForm(
                $form,
                $section,
                $sectionEntity,
                $this->sectionManager,
                $this->readSection
            );
        }

        $form->add('save', SubmitType::class);
        return $form->getForm();
    }

    public function hasRelationship(array $formData): array
    {
        $relationships = [];
        foreach ($formData as $key=>$data) {
            if (strpos($key, '_id')) {
                if (is_string($data)) {
                    $relationship = explode(':', $data);
                    $relationship = JitRelationship::fromFullyQualifiedClassNameAndId(
                        FullyQualifiedClassName::fromString($relationship[0]),
                        Id::fromInt((int)$relationship[1])
                    );
                    $relationships[] = $relationship;
                }

                if (is_array($data)) {
                    foreach ($data as $value) {
                        $relationship = explode(':', $value);
                        $relationship = JitRelationship::fromFullyQualifiedClassNameAndId(
                            FullyQualifiedClassName::fromString($relationship[0]),
                            Id::fromInt((int)$relationship[1])
                        );
                        $relationships[] = $relationship;
                    }
                }
            }
        }

        return $relationships;
    }

    /**
     * A section can be summoned by either it's handle: 'someCoolHandle'
     * or it's fully qualified class name: 'Vendor\Entity\SomeCoolEntity'
     * Make sure we fetch the entity config so we can get the FQCN from there
     * @param string $forHandle
     * @return SectionConfig
     */
    private function getSectionConfig(string $forHandle): SectionConfig
    {
        return $this->sectionManager->readByHandle(
            FullyQualifiedClassNameConverter::toHandle(
                FullyQualifiedClassName::fromString($forHandle)
            )
        )->getConfig();
    }

    /**
     * If you use SexyField with symfony you might want to inject the formFactory from the framework
     * If you use it stand-alone this will build a form factory right here.
     * @return FormFactory
     */
    private function getFormFactory(): FormFactory
    {
        $factory = $this->formFactory;
        if (empty($this->formFactory)) {
            $validatorBuilder = Validation::createValidatorBuilder();
            // Loads validator metadata from entity static method
            $validatorBuilder->addMethodMapping('loadValidatorMetadata');
            $validator = $validatorBuilder->getValidator();
            $session = new Session();
            $csrfGenerator = new UriSafeTokenGenerator();
            $csrfStorage = new SessionTokenStorage($session);
            $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);
            $factory = Forms::createFormFactoryBuilder()
                ->addExtension(new CsrfExtension($csrfManager))
                ->addExtension(new ValidatorExtension($validator))
                ->getFormFactory();
        }
        return $factory;
    }

    private function getSection(
        FullyQualifiedClassName $forHandle
    ): SectionInterface {
        return $this->sectionManager->readByHandle(
            FullyQualifiedClassNameConverter::toHandle($forHandle)
        );
    }

    private function getSectionEntity(
        FullyQualifiedClassName $forHandle,
        SectionInterface $section,
        Slug $slug = null,
        Id $id = null
    ) {
        if (!empty($slug)) {
            $sectionEntity = $this->readSection->read(ReadOptions::fromArray([
                ReadOptions::SECTION => $forHandle,
                ReadOptions::SLUG => $slug
            ]))->current();
        }

        if (!empty($id)) {
            $sectionEntity = $this->readSection->read(ReadOptions::fromArray([
                ReadOptions::SECTION => $forHandle,
                ReadOptions::ID => $id
            ]));
        }

        if (empty($sectionEntity)) {
            $sectionFullyQualifiedClassName = (string) $section->getConfig()->getFullyQualifiedClassName();
            $sectionEntity = new $sectionFullyQualifiedClassName;
        }

        return $sectionEntity;
    }
}
