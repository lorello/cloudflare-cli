#!/bin/bash

VERSION=$1

[ -z $VERSION ] && echo "Missing version parameter" && exit 1

#git tag $VERSION

box build

s3cmd cloudflare.phar s3://static.devops.it/cloudflare-cli/cloudflare-cli-{$VERSION}.phar

echo -e "\n{"
echo -e "    \"name\": \"cloudflare.phar\",\n"
echo -e "    \"sha1\": "
php -r "echo sha1_file('cloudflare.phar');"
echo -e ",\n"
echo -e "    \"url\": \"s3://static.devops.it/cloudflare-cli/cloudflare-cli-{$VERSION}.phar\","
echo -e "}\n"
