<?php

require 'vendor/autoload.php';

use Guzzle\Http\Client;
use Symfony\Component\Yaml\Yaml;


$config_file = getenv('HOME').'/.cloudflare.yaml';
$email = getenv('CF_USER');
$tkn = getenv('CF_TOKEN');

$client = new Client('https://www.cloudflare.com');

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


$a = 'fpurge_ts';


$request = $client->post('api_json.html', null, array(
    'tkn'       => $tkn,
    'email'     => $email,
    'a'         => $a,
    'z'         => 'cnms.it',
    'v'         => 1
));
$data = $request->send()->json();
print_r($data);

