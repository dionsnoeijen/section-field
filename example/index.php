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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\HttpKernel;

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
            __DIR__.'/src/Blog/config/xml',
            __DIR__.'/src/Relationships/config/xml'
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

/** @var \Tardigrades\SectionField\Service\SectionManagerInterface $sectionManager */
$sectionManager = $container->get('section_field.manager.doctrine.section_manager');

/** @var \Tardigrades\SectionField\Service\CreateSectionInterface $createSection */
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
$matched = true;
if (strpos($requestUri, '/blog/edit-blog') !== false) {
    $requestUri = '/blog/edit-blog';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}
if (strpos($requestUri, '/blog/article') !== false) {
    $requestUri = '/blog/article';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}
if (strpos($requestUri, '/blog/edit-author') !== false) {
    $requestUri = '/blog/edit-author';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}
if (strpos($requestUri, '/relationships/update-many-record-mto') !== false) {
    $requestUri = '/relationships/update-many-record-mto';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}
if (strpos($requestUri, '/relationships/update-one-record-mto') !== false) {
    $requestUri = '/relationships/update-one-record-mto';
    $slug = explode('/', $request->getRequestUri());
    $slug = $slug[count($slug) -1];
}

try {
    switch ($requestUri) {
        case '/':
            echo $templating->render('home.html.twig');
            break;
        case '/relationships':
            echo $templating->render('relationships.html.twig');
            break;
        case '/relationships/one-to-many':
            echo $templating->render('one-to-many.html.twig');
            break;
        case '/relationships/many-to-one':
            echo $templating->render('many-to-one.html.twig');
            break;
        case '/relationships/create-many-record-mto':
            echo $templating->render('create-many-record-mto.html.twig');
            break;
        case '/relationships/update-many-record-mto':
            echo $templating->render('update-many-record-mto.html.twig', [
                'slug' => $slug
            ]);
            break;
        case '/relationships/create-one-record-mto':
            echo $templating->render('create-one-record-mto.html.twig');
            break;
        case '/relationships/update-one-record-mto':
            echo $templating->render('update-one-record-mto.html.twig', [
                'slug' => $slug
            ]);
            break;
        case '/relationships/create-one-record-otm':
            echo $templating->render('create-one-record-otm.html.twig');
            break;
        case '/relationships/update-one-record-otm':
            echo $templating->render('update-one-record-otm.html.twig', [
                'slug' => $slug
            ]);
            break;
        case '/relationships/create-many-record-otm':
            echo $templating->render('create-many-record-otm.html.twig');
            break;
        case '/relationships/update-many-record-otm':
            echo $templating->render('update-many-record-otm.html.twig', [
                'slug' => $slug
            ]);
            break;
        case '/relationships/many-to-many':
            echo $templating->render('many-to-many.html.twig');
            break;
        case '/relationships/one-to-one':
            echo $templating->render('one-to-one.html.twig');
            break;
        case '/blog':
            echo $templating->render('blog.home.html.twig');
            break;
        case '/blog/create-blog':
            echo $templating->render('create-blog.html.twig');
            break;
        case '/blog/create-author':
            echo $templating->render('create-author.html.twig');
            break;
        case '/blog/edit-blog':
            echo $templating->render('edit-blog.html.twig', [
                'slug' => $slug
            ]);
            break;
        case '/blog/edit-author':
            echo $templating->render('edit-author.html.twig', [
                'slug' => $slug
            ]);
            break;
        case '/blog/article':
            echo $templating->render('detail.html.twig', [
                'slug' => $slug
            ]);
            break;
        default:
            $matched = false;
            break;
    }
} catch (\Exception $exception) {
    header("HTTP/1.0 404 Not Found");
    echo $templating->render('404.html.twig', [
        'slug' => $slug,
        'message' => $exception->getMessage()
    ]);
}

// ------------------------------
// Symfony routing
// ------------------------------
if (!$matched) {
    $yamlRoutingLoader = new \Symfony\Component\Routing\Loader\YamlFileLoader(
        new FileLocator([__DIR__ . '/../src/config/routing'])
    );
    $yamlRoutes = $yamlRoutingLoader->load('api.yml');

    $context = new RequestContext();
    $context->fromRequest($request);
    $ymlMatcher = new UrlMatcher($yamlRoutes, $context);

    $controllerResolver = new HttpKernel\Controller\ContainerControllerResolver($container);
    $argumentResolver = new HttpKernel\Controller\ArgumentResolver();

    try {
        $request->attributes->add($ymlMatcher->match($request->getPathInfo()));
        $controller = $controllerResolver->getController($request);
        $arguments = $argumentResolver->getArguments($request, $controller);

        $response = call_user_func_array($controller, $arguments);
    } catch (Routing\Exception\ResourceNotFoundException $e) {
        $response = new Response('Not Found', 404);
    } catch (Exception $e) {
        $response = new Response('An error occurred: ' . $e->getMessage(), 500);
    }

    $response->send();
}
