#!/usr/bin/env php
<?php
date_default_timezone_set('UTC');
set_time_limit(0);

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console as Console;
use Silex\Application;


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

$console = new Console\Application('CloudFlare API', '1.0.0');

$console->add(new Lorello\Command\PurgeCacheCommand($app, 'purge-cache'));

$console->run();

# interactive shell?
#$shell = new Console\Shell($app);
#$shell->run();
