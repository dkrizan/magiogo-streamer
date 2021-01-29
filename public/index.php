<?php

use App\UnknownChannelNameException;
use DI\Container;
use Predis\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use Noodlehaus\Config;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();

AppFactory::setContainer($container);
$app = AppFactory::create();

// Add Slim routing middleware
$app->addRoutingMiddleware();

//DI
$container->set('config', function ($c) {
    try {
        return Config::load(__DIR__ . '/../config.yaml');
    } catch (\Noodlehaus\Exception\FileNotFoundException $ex) {
        throw new \App\ConfigException('File "config.yaml" is missing in root of a project. Use "config.yaml.template" as template for your config.');
    }
});
$container->set('redis', function ($c) {
    $params = [
        'schema' => 'tcp',
        'host' => $c->get('config')->get('redis.host'),
        'port' => $c->get('config')->get('redis.port')
    ];
    return new Client($params);
});
$container->set('guzzle', function () {
    return new \GuzzleHttp\Client();
});
$container->set('scrapper', function ($c) {
    $connection = $c->get('redis');
    $authorization = $c->get('authorization');
    $channels = $c->get('channels');
    $guzzle = $c->get('guzzle');
    $config = $c->get('config');
    return new \App\Scrapper($connection, $authorization, $channels, $guzzle, $config);
});
$container->set('authorization', function ($c) {
    $connection = $c->get('redis');
    $config = $c->get('config');
    $guzzle = $c->get('guzzle');
    return new \App\Authorization($connection, $config, $guzzle);
});
$container->set('channels', function ($c) {
    $connection = $c->get('redis');
    $guzzle = $c->get('guzzle');
    $authorization = $c->get('authorization');
    $config = $c->get('config');
    return new \App\Channels($connection, $guzzle, $authorization, $config);
});

$container->set('loggerFactory', function ($c) {
    $settings =  [
        'name' => 'app',
        'path' => __DIR__ . '/../log',
        'filename' => 'app.log',
        'level' => \Monolog\Logger::DEBUG,
        'file_permission' => 0775
    ];
    return new \Libs\LoggerFactory($settings);
});

// Set the base path to run the app in a subdirectory.
// This path is used in urlFor().
$app->add(new BasePathMiddleware($app));

$loggerFactory = $app->getContainer()->get('loggerFactory');
$logger = $loggerFactory->addFileHandler('error.log')->createInstance('error');
$app->addErrorMiddleware($container->get('config')->get('debug', false), true, true, $logger);

// Define app routes
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('magio streams scraper');
    return $response;
})->setName('root');
$app->get('/stream/{channel}', function (Request $request, Response $response, $args) {
    try {
        $url = $this->get('scrapper')->fetchStreamUrl($args['channel']);
        $response->withStatus(307);
        return $response->withHeader('Location', $url);
    } catch (UnknownChannelNameException $ex) {
        $response->withStatus(404);
        $response->getBody()->write("Given channel id is not found.");
        return $response;
    }
})->setName('root');
$app->get('/channels', function (Request $request, Response $response, $args) {
    $channels = $this->get('channels')->getChannels();
    $response->getBody()->write(json_encode($channels));
    return $response->withHeader('Content-Type', 'application/json');
})->setName('root');

// Run app
$app->run();