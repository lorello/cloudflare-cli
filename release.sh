#!/bin/bash
#
# Script to bundle a new phar and public it on S3
#

VERSION=$1

[ -z $VERSION ] && echo "Missing version parameter" && exit 1

[ -f cloudflare.phar ] && rm cloudflare.phar
box build
[ ! -f cloudflare.phar ] && echo "\nMissing cloudflare.phar, build failed?\n" && exit 1

# Public file
s3cmd put cloudflare.phar s3://static.devops.it/cloudflare-cli/cloudflare-cli-{$VERSION}.phar

# Create fragment for versions.json
SHA1=$(php -r "echo sha1_file('cloudflare.phar');")
echo -e "\n  {"
echo -e "    \"name\": \"cloudflare.phar\","
echo -e "    \"sha1\": \"${SHA1}\","
echo -e "    \"url\": \"http://static.devops.it/cloudflare-cli/cloudflare-cli-${VERSION}.phar\","
echo -e "    \"version\": ${VERSION},"
echo -e "  }\n"

git tag $VERSION

echo "Manually update versions.json, then continue"
read WAIT

git commit -a -m "released version ${VERSION}"
git push

