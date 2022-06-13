<?php
// start classes PSR-4 autoloader
require_once( 'vendor/autoload.php' );
// use needed classes
use App\ThemeInit;
// boot theme core
$theme_startup = new ThemeInit();