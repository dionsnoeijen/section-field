<?php
declare (strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Templating\TemplateNameParser;

// -----------------------------
// Create the container
// -----------------------------

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$requestStack = new \Symfony\Component\HttpFoundation\RequestStack();
$requestStack->push($request);

$container = new ContainerBuilder();
$container
    ->register('doctrine.orm.entity_manager')
    ->setFactory([EntityManager::class, 'create'])
    ->setArguments([
        [
            'dbname' => 'sectionfield',
            'user' => 'root',
            'password' => 'eR83k1n8t0r',
            'host' => '127.0.0.1',
            'port' => '3306',
            'driver' => 'pdo_mysql'
        ],
        Setup::createXMLMetadataConfiguration([
            __DIR__.'/../src/config/xml',
            __DIR__.'/src/Blog/config/xml'
        ], true)
    ]);
$container->set('request_stack', $requestStack);


$sectionFieldExtension = new \Tardigrades\DependencyInjection\SectionFieldExtension();
$sectionFieldExtension->load([], $container);

$loader = new XmlFileLoader(
    $container,
    new FileLocator([
        __DIR__.'/src/config/service'
    ])
);
$loader->load('form.xml');

/** @var \Tardigrades\SectionField\Form\Form $form */
$form = $container->get('section_field.form');

/** @var \Tardigrades\SectionField\SectionFieldInterface\SectionManager $sectionManager */
$sectionManager = $container->get('section_field.manager.doctrine.section_manager');

/** @var \Tardigrades\SectionField\SectionFieldInterface\CreateSection $createSection */
$createSection = $container->get('section_field.create.section');

// -----------------------------
// Get the templating up and running
// -----------------------------

$defaultFormTheme = 'bootstrap_3_layout.html.twig';
$vendorDir = realpath(__DIR__.'/../vendor');
$appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
$vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());
$viewsDir = realpath(__DIR__.'/src/view');
$twig = new Twig\Environment(
    new Twig_Loader_Filesystem([
        $viewsDir,
        $vendorTwigBridgeDir.'/Resources/views/Form',
    ]), ['debug' => true]);
$formEngine = new TwigRendererEngine(array($defaultFormTheme), $twig);
$twig->addRuntimeLoader(new \Twig_FactoryRuntimeLoader(array(
    TwigRenderer::class => function () use ($formEngine) {
        return new TwigRenderer($formEngine);
    },
)));

// ... (see the previous CSRF Protection section for more information)

// add the FormExtension to Twig
$twig->addExtension(new FormExtension());
$twig->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension(
    new \Symfony\Component\Translation\Translator('en_EN')
));
$twig->addExtension(new Twig_Extension_Debug());

/** @var \Tardigrades\Twig\SectionTwigExtension $twigSectionExtension */
$twigSectionExtension = $container->get('section_field.twig.section');
$twig->addExtension($twigSectionExtension);

/** @var \Tardigrades\Twig\SectionFormTwigExtension $twigSectionFormExtension */
$twigSectionFormExtension = $container->get('section_field.twig.section_form');
$twig->addExtension($twigSectionFormExtension);

$templating = new \Symfony\Bridge\Twig\TwigEngine(
    $twig,
    new TemplateNameParser()
);

// ------------------------------
// Set up some amazing routing
// ------------------------------

$requestUri = $request->getRequestUri();
$slug = '';
if (strpos($requestUri, '/edit-blog') !== false) {
    $requestUri = '/edit-blog';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}
if (strpos($requestUri, '/article') !== false) {
    $requestUri = '/article';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}
if (strpos($requestUri, '/edit-author') !== false) {
    $requestUri = '/edit-author';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}

try {
    switch ($requestUri) {
        case '/':
            echo $templating->render('home.html.twig');
        break;
        case '/create-blog':
            echo $templating->render('create-blog.html.twig');
        break;
        case '/create-author':
            echo $templating->render('create-author.html.twig');
        break;
        case '/edit-blog':
            echo $templating->render('edit-blog.html.twig', [
                'slug' => $slug
            ]);
        break;
        case '/edit-author':
            echo $templating->render('edit-author.html.twig', [
                'slug' => $slug
            ]);
        break;
        case '/article':
            echo $templating->render('detail.html.twig', [
                'slug' => $slug
            ]);
        break;
    }
} catch (\Exception $exception) {
    header("HTTP/1.0 404 Not Found");
    echo $templating->render('404.html.twig', [
        'slug' => $slug,
        'message' => $exception->getMessage()
    ]);
}
