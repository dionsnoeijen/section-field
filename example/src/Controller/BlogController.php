<?php
declare (strict_types=1);

namespace Example\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;
use Tardigrades\SectionField\Form\Form;

class BlogController
{
    /** @var Form */
    private $form;

    /** @var EngineInterface */
    private $templating;

    public function __construct(
        EngineInterface $templating,
        Form $form
    ) {
        $this->form = $form;
        $this->templating = $templating;
    }

    public function createAction(Request $request)
    {
        return $this->templating->render('create-blog.html.twig');
    }

    public function editAction(Request $request)
    {
        $slug = explode('/', $request->getUri());

        return $this->templating->render('edit-blog.html.twig', [
            'slug' => $slug[count($slug) -1]
        ]);
    }
}
