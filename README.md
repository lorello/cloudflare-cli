cloudflare-cli
==============

Simple CLI for [CloudFlare](http://www.cloudflare.com)

## Requirements

 * PHP >= 5.3
 * [Composer](http://getcomposer.org)

## Setup

You must provide the credentials to access CF API

### Through environment variables

Just drop two variables in your `~/.bashrc`:

    CF_USER=<your_cloudflare_email>
    CF_TOKEN=<your_api_token>

### Through config file

Create a file in your home directory `~/.cloudflare.yaml`:

    email : <your_cloudflare_email> 
    token : <your_api_token>

## Usage

Only one command implemented at this time:

    cloudflare.php purge-cache <domain>





[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/lorello/cloudflare-cli/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

