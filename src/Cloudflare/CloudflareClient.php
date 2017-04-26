<?php

namespace Cloudflare;

use Guzzle\Common\Collection;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

/**
 * Cloudflare API Client.
 */
class CloudflareClient extends Client
{
    public static function factory($config = [])
    {
        // Provide a hash of default client configuration options
        $default = ['base_url' => 'https://www.cloudflare.com'];

        // The following values are required when creating the client
        $required = [
            //'user',
            //'token',
        ];

        // Merge in default settings and validate the config
        $config = Collection::fromConfig($config, $default, $required);

        // Create a new client
        $client = new self($config->get('base_url'), $config);

        // $client = $app['guzzle.client'];
        // $description = ServiceDescription::factory('src/cloudflare.json');
        // $client->setDescription($description);
        $description = ServiceDescription::factory('src/cloudflare.json');
        $client->setDescription($description);

        return $client;
    }
}
