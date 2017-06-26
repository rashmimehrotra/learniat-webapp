<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
define('EXIT_SUCCESS', 0); // no errors
define('EXIT_ERROR', 1); // generic error
define('EXIT_CONFIG', 3); // configuration error
define('EXIT_UNKNOWN_FILE', 4); // file not found
define('EXIT_UNKNOWN_CLASS', 5); // unknown class
define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
define('EXIT_USER_INPUT', 7); // invalid user input
define('EXIT_DATABASE', 8); // database error
define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


/**
 * Learniat Constant
 */
//$iniArray = parse_ini_file(__DIR__ . '/../../learniat.ini');
//var_dump($iniArray['server_IP']);exit;

define('LEARNIAT_IMAGE_PATH', 'http://54.251.104.13/images/sunprofile/');
define('IMAGE_SIZE_79', '79px');
define('LEARNIAT_IMAGE_BADGES_PATH', 'http://54.251.104.13/images/upload/badges/');

define('LEARNIAT_IMAGE_SCRIBBLE', 'http://54.251.104.13/images/');
define('LEARNIAT_IMAGE_RELATIVE_PATH', '/var/www/images/');
/*
define('LEARNIAT_IMAGE_SCRIBBLE', 'http://localhost/images/');
define('LEARNIAT_IMAGE_RELATIVE_PATH', '/var/www/html/images/');
*/
define('WEB_APP_IMAGE_PATH', 'uploads_webapp/');
define('CALENDER_DAYS', 5);

define('DUMMY_TOPIC_ID', 264);


//Image
define('CROP_MIN_WIDTH', 780);
define('CROP_MIN_HEIGHT', 520);
define('CROP_MAX_WIDTH', 1500);
define('CROP_MAX_HEIGHT', 1000);
define('CROP_MIN_WIDTH_RETINA', 1500);
define('CROP_MIN_HEIGHT_RETINA', 1000);


//PROFILE PIC
define('PROFILE_PIC_1', 44);
define('PROFILE_PIC_2', 88);
define('PROFILE_PIC_3', 176);
define('PROFILE_PIC_4', 240);
define('PROFILE_PIC_5', 480);