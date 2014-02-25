<?php

date_default_timezone_set('UTC');
set_time_limit(0);

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console as Console;
use Silex\Application;
use Guzzle\GuzzleServiceProvider;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

$email = getenv('CF_USER');
$tkn = getenv('CF_TOKEN');

if (empty($email) or empty($tkn)) {
    $config_file = getenv('HOME').'/.cloudflare.yaml';
    if (file_exists($config_file)) {
        $parsed = Yaml::parse($config_file);
        $email = $parsed['email'];
        $tkn = $parsed['token'];
    } else {
        echo("Missing configuration");
        exit(1);
    }
}

$app = new Silex\Application();
$app['email']=$email;
$app['token']=$tkn;

$console = new Console\Application('CloudFlare CLI', '0.1');

$app->register(new GuzzleServiceProvider(), array(
    'guzzle.services' => 'src/services.json',
));


$console->add(new Lorello\Command\PurgeCacheCommand($app, 'cache:purge'));

/*
$builder = $app['guzzle'];
$cio = $builder->get('cio');
*/

$description = ServiceDescription::factory('src/cloudflare.json');
$client = $app['guzzle.client'];
$client->setDescription($description);
$command = $client->getCommand('GetUser');
$responseModel = $client->execute($command);
var_dump($responseModel);