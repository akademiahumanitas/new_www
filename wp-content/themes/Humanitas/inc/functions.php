<?php
// Require all files in the functions folder
foreach ( glob( get_theme_file_path( '/inc/functions/*.php' ) ) as $file ) {
  require $file;
}
