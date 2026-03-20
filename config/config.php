<?php
/* START SESSION ONLY ONCE */
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

/* BASE URL */
if(!defined('BASE_URL')){
    define('BASE_URL', 'http://localhost/ServeSmart/');
}

/* DATABASE CONFIG */
if(!defined('DB_HOST')){
    define('DB_HOST', 'localhost');
}

if(!defined('DB_USER')){
    define('DB_USER', 'root');
}

if(!defined('DB_PASS')){
    define('DB_PASS', '');
}

if(!defined('DB_NAME')){
    define('DB_NAME', 'servesmart');
}
?>