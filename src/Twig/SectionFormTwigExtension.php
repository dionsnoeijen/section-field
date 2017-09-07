<?php
declare (strict_types=1);

namespace Tardigrades\Twig;

use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\SectionFieldInterface\CreateSection;
use Tardigrades\SectionField\SectionFieldInterface\Form;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\JitRelationship;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\SectionFormOptions;
use Twig_Extension;
use Twig_Function;

class SectionFormTwigExtension extends Twig_Extension
{
    /** @var SectionManager */
    private $sectionManager;

    /** @var Form */
    private $form;

    /** @var CreateSection */
    private $createSection;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        SectionManager $sectionManager,
        CreateSection $createSection,
        Form $form,
        RequestStack $requestStack
    ) {
        $this->sectionManager = $sectionManager;
        $this->createSection = $createSection;
        $this->form = $form;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return array(
            new Twig_Function(
                'sectionForm',
                array($this, 'sectionForm')
            ),
            new Twig_Function(
                'formJavascript',
                array($this, 'formJavascript')
            )
        );
    }

    public function sectionForm(
        string $forHandle,
        array $sectionFormOptions = []
    ): FormView {

        $sectionFormOptions = SectionFormOptions::fromArray($sectionFormOptions);

        $form = $this->form->buildFormForSection(
            $forHandle,
            $sectionFormOptions
        );
        $form->handleRequest();

        if ($form->isSubmitted() &&
            $form->isValid()
        ) {
            $data = $form->getData();
            $request = $this->requestStack->getCurrentRequest();
            $relationships = $this->hasRelationship($request->get('form'));
            $this->createSection->save($data, $relationships);

            try {
                $redirect = $sectionFormOptions->getRedirect();
            } catch (\Exception $exception) {
                $redirect = '/';
            }
            header('Location: ' . $redirect);
            exit;
        }

        return $form->createView();
    }

    /**
     * A form can have javascript powered fields,
     * fetch them to bring them to your template
     */
    public function formJavascript()
    {

    }

    private function hasRelationship($formData): array
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
}
