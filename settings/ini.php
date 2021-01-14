<?php

ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);

// Error logging (Disabled in .htaccess)
# supress php errors
ini_set("max_execution_time", 600);
ini_set("display_startup_errors", 0);
ini_set("display_errors", 0);
ini_set("html_errors", 0);
ini_set("docref_root", 0);
ini_set("docref_ext", 0);

# enable PHP error logging
ini_set("log_errors", 1);
ini_set("error_log", "./logs/php-errors.log");

# max file size
ini_set('post_max_size', '50M');
ini_set('upload_max_filesize', '50M');

