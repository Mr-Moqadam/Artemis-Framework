<?php
/**************** Config ********************/
// root folder
$config['base'] = '/artemis/';

$config['base_url'] = 'http://127.0.0.1'.$config['base'];

// server db
$config['server'] = 'localhost';

// username db
$config['username'] = 'root';
// pass db
$config['password'] = '';

/**
 * Database name
 * 
 */
$config['database'] = 'ar_blog';

/**
 *  Driver name
 *
 * PDO_Mysql , PDO_Mssql , PDO_Sqllite , ... 
 * 
 */
$config['driver'] = 'PDO_Mysql';

// set default controller
$config['base_controller'] = 'index';

$config['theme'] = 'default';

ini_set('ignore_repeated_errors', 1);
// Security
@ini_set('register_globals', 0); // PHP < 6
ini_set('mbstring.func_overload', 0); // http://bugs.php.net/bug.php?id=30766
ini_set('session.cookie_httponly', 1);
ini_set('session.use_trans_sid', 0);
// Compability
ini_set('max_execution_time', 0);
@ini_set('memory_limit', '128M');
@ini_set('zend.ze1_compatibility_mode', 0); // PHP < 6
ini_set('session.auto_start', 0);