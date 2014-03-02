<?php

namespace Lorello;


use Guzzle\Common\Collection;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

/**
 * A simple Twitter API client
 */
class CloudflareClient extends Client
{
    public static function factory($config = array())
    {
        // Provide a hash of default client configuration options
        $default = array('base_url' => 'https://www.cloudflare.com');

        // The following values are required when creating the client
        $required = array(
            'u',
            'tkn',
        );

        // Merge in default settings and validate the config
        $config = Collection::fromConfig($config, $default, $required);

        // Create a new client
        $client = new self($config->get('base_url'), $config);

        return $client;
    }
}
