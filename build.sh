#!/usr/bin/env bash

if [ ! -f ./composer.phar ]; then
    wget getcomposer.org/composer.phar
fi

php composer.phar install -d plib/library

rm -f spamexperts-extension.zip

zip -r spamexperts-extension.zip \
    --exclude="*/\.DS_Store" \
    --exclude=build.sh \
    --exclude=composer.phar \
    --exclude=codecept.phar \
    --exclude=codeception.yml \
    --exclude=*tests* \
    --exclude=docs/* \
    --exclude=library/composer.json \
    --exclude=library/composer.lock \
    --exclude=spamexperts-extension.zip \
    --exclude="*/\.*" ./*
