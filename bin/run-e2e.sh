#!/usr/bin/env bash
npm install --prefix ./cypress

# Install dependencies.
composer install --no-dev
npm ci
npm run build
npm run dist

export DOCKER_FILE=docker-compose.ci.yml

# Bring stack up.
docker-compose -f $DOCKER_FILE up -d
sleep 15

# Run setup
docker-compose -f $DOCKER_FILE run --rm -u root cli /var/www/html/bin/cli-setup.sh
