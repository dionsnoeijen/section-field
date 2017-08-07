<?php
declare (strict_types=1);

namespace Example\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;
use Tardigrades\SectionField\Form\Form;

class IndexController
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

    public function indexAction(Request $request)
    {
        return $this->templating->render('home.html.twig');
    }
}
