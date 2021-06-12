CLI='cli'
CONTAINER='wordpress'
DATABASE='mysql'
SITE_TITLE='Astra Dev'
$HOST_PORT=$(dc port wordpress 80 | awk -F : '{printf $2}')

php -d memory_limit=1024M "$(which wp)" package install anhskohbo/wp-cli-themecheck --allow-root
echo "Installing WP"
wp core install --title="$SITE_TITLE" --admin_user=admin --admin_password=password --admin_email=test@test.com --skip-email --url=http://localhost:$HOST_PORT --allow-root
wp plugin install theme-check --activate --allow-root
wp themecheck --theme=astra --no-interactive --allow-root
