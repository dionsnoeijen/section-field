<?php
declare (strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Templating\TemplateNameParser;

// Container
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
        Setup::createXMLMetadataConfiguration([__DIR__."/../src/config/xml"], true)
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

$templating = new \Symfony\Bridge\Twig\TwigEngine(
    new \Twig\Environment(
        new Twig_Loader_Filesystem(__DIR__ . '/src/view/')
    ),
    new TemplateNameParser()
);

// ------------------------------
// Set up some amazing routing
// ------------------------------
$requestUri = $_SERVER['REQUEST_URI'];
if (strpos($requestUri, '/edit-blog') !== false) {
    $requestUri = '/edit-blog';
}

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$indexController = new \Example\Controller\IndexController($templating, $form);
$blogController = new \Example\Controller\BlogController($templating, $form);

switch ($requestUri) {
    case '/':
        echo $indexController->indexAction($request);
        break;
    case '/create-blog':
        echo $blogController->createAction($request);
        break;
    case '/edit-blog':
        echo $blogController->editAction($request);
        break;
}
