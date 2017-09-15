<?php
declare (strict_types=1);

namespace Tardigrades\Twig;

use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Tardigrades\SectionField\Service\CreateSectionInterface;
use Tardigrades\SectionField\Service\FormInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\SectionFormOptions;
use Twig_Extension;
use Twig_Function;

class SectionFormTwigExtension extends Twig_Extension
{
    /** @var SectionManagerInterface */
    private $sectionManager;

    /** @var Form */
    private $form;

    /** @var CreateSection */
    private $createSection;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        SectionManagerInterface $sectionManager,
        CreateSectionInterface $createSection,
        FormInterface $form,
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

            $relationships = $this->form->hasRelationship($request->get('form'));
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
}
