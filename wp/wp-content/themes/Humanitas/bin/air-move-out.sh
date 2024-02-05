#!/bin/bash
# A script for moving all dev files out of the theme for testing with Theme Check plugin
txtbold=$(tput bold)
boldyellow=${txtbold}$(tput setaf 3)
boldgreen=${txtbold}$(tput setaf 2)
boldwhite=${txtbold}$(tput setaf 7)
yellow=$(tput setaf 3)
green=$(tput setaf 2)
white=$(tput setaf 7)
txtreset=$(tput sgr0)

echo "${YELLOW}Moving dev files out...${TXTRESET}"
mkdir -p $HOME/air-temp
find . -name '.DS_Store' -type f -delete
find ../ -name '.DS_Store' -type f -delete
rm /var/www/airdev/content/themes/humanitas/sass/components/.gitkeep $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/sass/modules/.gitkeep $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.hintrc $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.stylelintignore $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.nvmrc $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.eslintrc.js $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.browserslistrc $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.vscode $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.svgo.yml $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.accessibilityrc $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.git $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.gitignore $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.jshintignore $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.travis.yml $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/package.json $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/package-lock.json $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/phpcs.xml $HOME/air-temp/
sudo mv /var/www/airdev/content/themes/humanitas/node_modules $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/gulpfile.js $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/bin $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/content $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/__MACOSX $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.scss-lint.yml $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/front-page.php $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/README.md $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.stylelintrc $HOME/air-temp/
mv /var/www/airdev/content/themes/humanitas/.editorconfig $HOME/air-temp/
mkdir -p $HOME/air-temp/template-parts
mkdir -p $HOME/air-temp/template-parts/header
mkdir -p $HOME/air-temp/template-parts/footer

# Remove custom stuff that are not part of an official WordPress theme in WP theme directory,
# Because:
# REQUIRED: The theme uses the register_taxonomy() function, which is plugin-territory functionality.
# REQUIRED: The theme uses the register_post_type() function, which is plugin-territory functionality.
rm /var/www/airdev/content/themes/humanitas/inc/includes/taxonomy.php
rm /var/www/airdev/content/themes/humanitas/inc/includes/post-type.php

# Screenshot, related to: https://themes.trac.wordpress.org/ticket/100180#comment:2
mv /var/www/airdev/content/themes/humanitas/screenshot.png $HOME/air-temp/
cd /var/www/airdev/content/themes/humanitas/
wget https://i.imgur.com/idVvQKv.png
mv -v idVvQKv.png screenshot.png

# Moving to bin dir
cd $HOME/air-temp/bin

echo "
${boldgreen}Done! Next steps:${TXTRESET}"
echo "
${boldwhite}1. Click the Check it -button: http://airdev.test/wp/wp-admin/themes.php?page=themecheck
2. Run: sh air-pack.sh (this also runs air-move-in.sh)
3. Follow instructions
${TXTRESET} "
