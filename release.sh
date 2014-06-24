#!/bin/bash
#
# Script to bundle a new phar and public it on S3
#

VERSION=$1

[ -z $VERSION ] && echo "Missing version parameter" && exit 1


box build

s3cmd cloudflare.phar s3://static.devops.it/cloudflare-cli/cloudflare-cli-{$VERSION}.phar

SHA1=$(php -r "echo sha1_file('cloudflare.phar');")
echo -e "\n{"
echo -e "    \"name\": \"cloudflare.phar\",\n"
echo -e "    \"sha1\": $SHA1"
echo -e ",\n"
echo -e "    \"url\": \"http://static.devops.it/cloudflare-cli/cloudflare-cli-${VERSION}.phar\","
echo -e "}\n"


git tag $VERSION


