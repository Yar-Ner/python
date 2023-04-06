<?php
declare(strict_types=1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

$hcurl = @curl_init();
global $agi;  

if (!$hcurl) {
    die("Critical error - no curl.");
}    

curl_setopt($hcurl, CURLOPT_URL, "http://185.10.129.195:18080/test_uat_1/hs/makrab/contractors");

curl_setopt($hcurl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($hcurl, CURLOPT_USERPWD, "Администратор:Kvesta21");

curl_setopt($hcurl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($hcurl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($hcurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($hcurl, CURLOPT_TIMEOUT, 30);

$body = curl_exec($hcurl);

$data = json_decode($body, TRUE);

var_dump($data);

curl_close($hcurl);


?>