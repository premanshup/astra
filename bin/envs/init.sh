#!/usr/bin/env bash
WP_ENV=${1-default}
WP_VERSION=${2-latest}
SKIP_CACHE=${3-no}
ZIP_URL=${4}

if [ ! -n "$ZIP_URL" ]
then
	npm install
	# Install dependencies.
	composer install --no-dev
	grunt release
	ls
	echo $PWD
	ZIP_URL="/home/runner/work/astra/astra/astra-3.5.0.zip"
fi
export DOCKER_FILE=docker-compose.ci.yml

# Bring stack up.
docker-compose -f $DOCKER_FILE up -d

# Wait for mysql container to be ready.
while docker-compose -f $DOCKER_FILE run --rm -u root cli wp --allow-root db check ; [ $? -ne 0 ];  do
	  echo "Waiting for db to be ready... "
    sleep 1
done

# Run setup
echo "Setting up environment $WP_ENV"

docker-compose -f $DOCKER_FILE run  --rm -u root cli bash -c "/var/www/html/bin/envs/cli-setup.sh $ZIP_URL $WP_VERSION $WP_ENV $SKIP_CACHE"
