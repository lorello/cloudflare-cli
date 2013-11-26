cloudflare-cli
==============

Simple CLI for (CloudFlare)[http://www.cloudflare.com]

## Requirements

 * PHP >= 5.3
 * (Composer)[http://getcomposer.org]

## Setup

You must provide the credentials to access CF API

### Through environment variables

Just drop two variables in your `.bashrc`:

    CF_USER=<your_cloudflare_email>
    CF_TOKEN=<your_api_token>

### Through config file

Create a file in `~/.cloudflare.yaml`

    email : <your_cloudflare_email> 
    token : <your_api_token>

## Use



