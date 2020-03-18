<?php
// MUTE NOTICES
error_reporting(E_ALL & ~E_NOTICE);
 
// DATABASE SETTINGS - CHANGE THESE TO YOUR OWN
define('DB_HOST', 'localhost');
define('DB_NAME', 'events');
define('DB_CHARSET', 'utf8');
define('DB_USER', 'admin');
define('DB_PASSWORD', 'admin');

// AUTO PATH
define('PATH_LIB', __DIR__ . DIRECTORY_SEPARATOR);
?>