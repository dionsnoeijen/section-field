<?php
declare (strict_types=1);

namespace Tardigrades\Twig;

use Symfony\Component\Form\FormView;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\SectionFieldInterface\CreateSection;
use Tardigrades\SectionField\SectionFieldInterface\Form;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\ReadOptions;
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

    /** @var ReadSection */
    private $readSection;

    public function __construct(
        SectionManager $sectionManager,
        CreateSection $createSection,
        ReadSection $readSection,
        Form $form
    ) {
        $this->sectionManager = $sectionManager;
        $this->createSection = $createSection;
        $this->readSection = $readSection;
        $this->form = $form;
    }

    public function getFunctions(): array
    {
        return array(
            new Twig_Function('sectionForm', array($this, 'sectionForm'))
        );
    }

    public function sectionForm(string $forHandle, string $slug = null): FormView
    {
        /** @var Section $blog */
        $blog = $this->sectionManager->readByHandle(
            FullyQualifiedClassNameConverter::toHandle(
                FullyQualifiedClassName::create($forHandle)
            )
        );

        $entry = null;
        if (!empty($slug)) {
            $entry = $this->readSection->read(ReadOptions::fromArray([
                'section' => $forHandle,
                'slug' => $slug
            ]))->current();
        }

        $form = $this->form->buildFormForSection($blog, $entry);
        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->createSection->save($data);
            //return new RedirectResponse('/create-blog');
        }

        return $form->createView();
    }
}
