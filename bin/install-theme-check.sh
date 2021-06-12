php -d memory_limit=1024M "$(which wp)" package install anhskohbo/wp-cli-themecheck --allow-root
echo "Installing WP"
wp  --allow-root core install --url=http://127.0.0.1:8889 --title=SandboxSite --admin_user=admin --admin_password=admin --admin_email=admin@admin.com
wp plugin install theme-check --activate --allow-root
wp themecheck --theme=astra --no-interactive --allow-root
