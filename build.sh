#!/usr/bin/env bash

set -e

if [ ! -f ./composer.phar ]; then
  wget getcomposer.org/composer.phar
fi

php composer.phar install --no-dev -d plib/library

rm -f spamexperts-extension.zip

zip -r spamexperts-extension.zip \
  --exclude="*/\.DS_Store" \
  --exclude=build.sh \
  --exclude=composer.phar \
  --exclude=codecept.phar \
  --exclude=codeception.yml \
  --exclude=phpmd.phar \
  --exclude=*tests* \
  --exclude=docs/* \
  --exclude=plib/library/composer.json \
  --exclude=plib/library/composer.lock \
  --exclude=spamexperts-extension.zip \
  --exclude="*/\.*" ./*
