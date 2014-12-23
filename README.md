cloudflare-cli
==============

Simple CLI for [CloudFlare](http://www.cloudflare.com)

[![build status](https://ci.sftc.it/projects/25/status.png?ref=master)](https://ci.sftc.it/projects/25?ref=master)

## Requirements

 * PHP >= 5.3
 * [Composer](http://getcomposer.org)

## Setup

You must provide the credentials to access [CF API](http://www.cloudflare.com/docs/client-api.html)

### Through environment variables

Just drop two variables in your `~/.bashrc`:

    CF_USER=<your_cloudflare_email>
    CF_TOKEN=<your_api_token>

### Through config file

Create a file in your home directory `~/.cloudflare.yaml`:

    email : <your_cloudflare_email>
    token : <your_api_token>

## Usage

Only a few commands are implemented at this time, this is the help command:
```
CloudFlare CLI version @package_version@

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message.
  --quiet          -q Do not output any message.
  --verbose        -v|vv|vvv Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
  --version        -V Display this application version.
  --ansi              Force ANSI output.
  --no-ansi           Disable ANSI output.
  --no-interaction -n Do not ask any interactive question.

Available commands:
  help           Displays help for a command
  list           Lists commands
  update         Updates the application.
cache
  cache:purge    Purge cache of a specific domain
dev
  dev:off        Toggle dev mode On or Off
  dev:on         Toggle dev mode On or Off
dns
  dns:list       Get all Dns records for a specific domain
zone
  zone:details   Get details about the specified zone
  zone:get       Get current settings of the specified zone
  zone:list      Lists all domains in a CloudFlare account along with other data.
```
