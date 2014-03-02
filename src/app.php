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
    'guzzle.services' => __DIR__ . '/services.json',
));

$command = $app['guzzle']['cf'];

/*
$client = $app['guzzle.client'];
$description = ServiceDescription::factory('src/cloudflare.json');
$client->setDescription($description);
*/
$console->add(new Lorello\Command\PurgeCacheCommand($app, 'cache:purge'));
$console->add(new Lorello\Command\GetDnsCommand($app, 'dns:get'));
