<?php

date_default_timezone_set('UTC');
set_time_limit(0);

require __DIR__.'/../vendor/autoload.php';

use Guzzle\GuzzleServiceProvider;
use KevinGH\Amend\Command;
use KevinGH\Amend\Helper;
use Knp\Provider\ConsoleServiceProvider;
use Symfony\Component\Console as Console;
use Symfony\Component\Yaml\Yaml;

$email = getenv('CF_USER');
$tkn = getenv('CF_TOKEN');

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows OS detected
  $config_path = getenv('HOMEPATH');
} else {
    $config_path = getenv('HOME');
}

if (empty($email) or empty($tkn)) {
    $config_file = $config_path.DIRECTORY_SEPARATOR.'.cloudflare.yaml';
    if (file_exists($config_file)) {
        $parsed = Yaml::parse($config_file);
        $email = $parsed['email'];
        $tkn = $parsed['token'];
    }
}

if (empty($email) or empty($tkn)) {
    echo "\nMissing configuration file: $config_file\n";
    echo "\nConfig file format:\n".
      "\n---8<-------------8<------------\n".
      "email: address@domain.tld\ntoken: TOKEN\n".
      "\n---8<-------------8<------------\n";
    exit(1);
}

$app = new Silex\Application();
$app['cf.user'] = $email;
$app['cf.token'] = $tkn;

$app->register(
    new GuzzleServiceProvider(),
    [
        'guzzle.services' => __DIR__.'/services.json',
    ]
);

// package_version si managed by phar builder
$app->register(
    new ConsoleServiceProvider(),
    [
        'console.name'              => 'CloudFlare CLI',
        'console.version'           => '@package_version@',
        'console.project_directory' => __DIR__.'/..',
    ]
);

$console = $app['console'];
$console->add(new Cloudflare\Command\PurgeCacheCommand($app, 'cache:purge'));
$console->add(new Cloudflare\Command\DnsGetCommand($app, 'dns:list'));

// TODO
$console->add(new Cloudflare\Command\DnsAddCommand($app, 'dns:add'));
// $console->add(new Cloudflare\Command\DnsUpdateCommand($app, 'dns:update'));
// $console->add(new Cloudflare\Command\DnsDeleteCommand($app, 'dns:delete'));
//
// $console->add(new Cloudflare\Command\VistorRecentCommand($app, 'visitor:recent'));
// $console->add(new Cloudflare\Command\VisitorScoreCommand($app, 'visitor:score'));
// $console->add(new Cloudflare\Command\VisitorWhitelistCommand($app, 'visitor:whitelist'));
// $console->add(new Cloudflare\Command\VisitorBanCommand($app, 'visitor:ban'));
// $console->add(new Cloudflare\Command\VisitorUnbanCommand($app, 'visitor:unban'));
//
$console->add(new Cloudflare\Command\ZoneListCommand($app, 'zone:list'));
$console->add(new Cloudflare\Command\ZoneDetailsCommand($app, 'zone:details'));
$console->add(new Cloudflare\Command\ZoneGetCommand($app, 'zone:get'));
// security-level, cache-level, ipv6, devmode, rocket-loader, minify, mirage
// $console->add(new Cloudflare\Command\ZoneSetCommand($app, 'zone:set'));

$console->add(new Cloudflare\Command\ZoneSetDevModeCommand($app, 'dev:on'));
$console->add(new Cloudflare\Command\ZoneSetDevModeCommand($app, 'dev:off'));
//
// $console->add(new Cloudflare\Command\StatsGetCommand($app, 'stats:get'));

$updateCommand = new Command('update');
$updateCommand->setManifestUri('https://raw.githubusercontent.com/lorello/cloudflare-cli/master/versions.json');
$console->getHelperSet()->set(new Helper());
$console->add($updateCommand);

//$console->add(new Cloudflare\Command\OpenIssueCommand($app, 'issue:open'));
