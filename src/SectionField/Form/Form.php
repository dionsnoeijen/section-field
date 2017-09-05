<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Forms;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldTypeInterface\FieldType;
use Tardigrades\FieldType\Slug\ValueObject\Slug;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\SectionFieldInterface\Form as SectionFormInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\ReadOptions;
use Tardigrades\SectionField\ValueObject\SectionFormOptions;

class Form implements SectionFormInterface
{
    /** @var SectionManager */
    private $sectionManager;

    /** @var FormFactory */
    private $formFactory;

    /** @var ReadSection */
    private $readSection;

    public function __construct(
        SectionManager $sectionManager,
        ReadSection $readSection,
        FormFactory $formFactory = null
    ) {
        $this->sectionManager = $sectionManager;
        $this->readSection = $readSection;
        $this->formFactory = $formFactory;
    }

    public function buildFormForSection(
        FullyQualifiedClassName $forHandle,
        SectionFormOptions $sectionFormOptions
    ): FormInterface {

        $section = $this->getSection($forHandle);

        try {
            $slug = $sectionFormOptions->getSlug();
        } catch (\Exception $exception) {
            $slug = null;
        }

        $sectionEntity = $this->getSectionEntity($forHandle, $section, $slug);

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
                    'csrf_protection' => true,
                    'csrf_field_name' => '_token',
                    // a unique key to help generate the secret token
                    'csrf_token_id'   => 'tardigrades'
                ]
            );

        /** @var Field $field */
        foreach ($section->getFields() as $field) {
            $fieldTypeFullyQualifiedClassName = (string) $field
                ->getFieldType()
                ->getFullyQualifiedClassName();
            /** @var FieldType $fieldType */
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
    ): Section {
        return $this->sectionManager->readByHandle(
            FullyQualifiedClassNameConverter::toHandle($forHandle)
        );
    }

    private function getSectionEntity(
        FullyQualifiedClassName $forHandle,
        Section $section,
        Slug $slug = null
    ) {
        if (!empty($slug)) {
            $sectionEntity = $this->readSection->read(ReadOptions::fromArray([
                'section' => $forHandle,
                'slug' => $slug
            ]))->current();
        }

        if (empty($sectionEntity)) {
            $sectionFullyQualifiedClassName = (string) $section->getConfig()->getFullyQualifiedClassName();
            $sectionEntity = new $sectionFullyQualifiedClassName;
        }

        return $sectionEntity;
    }
}
