<?php

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console as Console;


$config_file = getenv('HOME').'/.cloudflare.yaml';
$email = getenv('CF_USER');
$tkn = getenv('CF_TOKEN');


if (empty($email) or empty($tkn)) {
  if (file_exists($config_file)) {
    $parsed = Yaml::parse($config_file);
    $email = $parsed['email'];
    $tkn = $parsed['token'];
  } else {
    echo("Missing configuration");
    exit(1);
  }
}



$app = new Console\Application('CloudFlare API', '1.0.0');
$app->add(new Lorello\Command\PurgeCacheCommand('purge-cache'));
$app->run();

# interactive shell?
#$shell = new Console\Shell($app);
#$shell->run();
