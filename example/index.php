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

$templating = new \Symfony\Bridge\Twig\TwigEngine(
    $twig,
    new TemplateNameParser()
);

// ------------------------------
// Set up some amazing routing
// ------------------------------

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$requestUri = $request->getRequestUri();
$slug = '';
if (strpos($requestUri, '/edit-blog') !== false) {
    $requestUri = '/edit-blog';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}

$indexController = new \Example\Controller\IndexController($templating, $form);
$blogController = new \Example\Controller\BlogController(
    $templating,
    $form,
    $sectionManager,
    $createSection
);

switch ($requestUri) {
    case '/':
        echo $indexController->indexAction($request);
        break;
    case '/create-blog':
        echo $blogController->createAction($request);
        break;
    case '/edit-blog':
        echo $blogController->editAction($slug, $request);
        break;
}
