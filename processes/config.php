<?php
// database hostname, you don't usually need to change this
define('db_host', 'localhost:3307');
// database username
define('db_user', 'root');
// database password
define('db_pass', '');
// database name
define('db_name', 'main_db');
// database charset, change this only if utf8 is not supported by your language
define('db_charset', 'utf8');
// Email activation variables
// account activation required?
define('account_activation', false);
// Change "Your Company Name" and "yourdomain.com", do not remove the < and >
define('mail_from', 'Your Company Name <noreply@yourdomain.com>');
// Link to activation file, update this
define('activation_link', 'http://yourdomain.com/phplogin/activate.php');