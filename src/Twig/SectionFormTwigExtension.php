<?php
declare (strict_types=1);

namespace Tardigrades\Twig;

use Example\Blog\Entity\Blog;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Tardigrades\FieldType\Slug\ValueObject\Slug;
use Tardigrades\SectionField\SectionFieldInterface\CreateSection;
use Tardigrades\SectionField\SectionFieldInterface\Form;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\JitRelationship;
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
            )
        );
    }

    public function sectionForm(string $forHandle, string $slug = null): FormView
    {
        $form = $this->form->buildFormForSection(
            FullyQualifiedClassName::create($forHandle),
            !empty($slug) ? Slug::fromString($slug) : null
        );
        $form->handleRequest();

        if ($form->isSubmitted() &&
            $form->isValid()
        ) {
            $data = $form->getData();

            $request = $this->requestStack->getCurrentRequest();
            $relationships = $this->hasRelationship($request->get('form'));

            $this->createSection->save($data, $relationships);
        }

        return $form->createView();
    }

    private function hasRelationship($formData): array
    {
        $relationships = [];
        foreach ($formData as $key=>$data) {
            if (strpos($key, '_id')) {
                $relationship = explode(':', $data);
                $relationship = JitRelationship::fromFullyQualifiedClassNameAndId(
                    FullyQualifiedClassName::create($relationship[0]),
                    Id::create((int) $relationship[1])
                );

                $relationships[] = $relationship;
            }
        }

        return $relationships;
    }
}
