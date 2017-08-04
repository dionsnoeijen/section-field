<?php
declare (strict_types=1);

namespace Example\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\SectionField\Form\Form;
use Tardigrades\SectionField\SectionFieldInterface\CreateSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class BlogController
{
    /** @var Form */
    private $form;

    /** @var EngineInterface */
    private $templating;

    /** @var SectionManager */
    private $sectionManager;

    /** @var CreateSection */
    private $createSection;

    public function __construct(
        EngineInterface $templating,
        Form $form,
        SectionManager $sectionManager,
        CreateSection $createSection
    ) {
        $this->form = $form;
        $this->templating = $templating;
        $this->sectionManager = $sectionManager;
        $this->createSection = $createSection;
    }

    public function createAction(Request $request)
    {
        /** @var Section $blog */
        $blog = $this->sectionManager->readByHandle('blog');
        $form = $this->form->buildFormForSection($blog);

        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->createSection->save($data);

            return new RedirectResponse('/create-blog');
        }

        return $this->templating->render('create-blog.html.twig', [
            'form' =>  $form->createView()
        ]);
    }

    public function editAction(string $slug, Request $request)
    {
        return $this->templating->render('edit-blog.html.twig', [
            'slug' => $slug
        ]);
    }
}
