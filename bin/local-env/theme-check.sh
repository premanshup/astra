php -d memory_limit=1024M "$(which wp)" package install anhskohbo/wp-cli-themecheck --allow-root
wp plugin install theme-check
wp plugin activate theme-check
wp themecheck --theme=astra --no-interactive
