#!/bin/bash

echo "rebuilding database ..."
php bin/console doctrine:schema:drop -n -q --force --full-database
rm migrations/*.php
php bin/console make:migration
php bin/console doctrine:migrations:migrate -n -q

if [ "$1" == "--seed" ]
then
php bin/console doctrine:fixtures:load -n -q
echo "seeding data ..."
fi

echo "rebuilding done"

