<?php
/*
Plugin Name: mySTAT
Plugin URI: http://my-stat.com
Description: mySTAT is a flexible and versatile system intended for accumulation and analysis of the site attendance statistics. mySTAT suits to upcoming projects perfectly. There are more than 50 reports available in the system. The system is easy to install and to set up; it allows counting all the visitors of your web-site - both humans and robots. All visits data is stored at your server, which meets safety and confidentiality requirements.
Version: 3.0
Author: Smyshlaev Evgeniy
Author URI: http://hide.com.ua
Text Domain: mystat
Domain Path: ./language/
*/

if(!defined('MYSTAT_VERSION')){
  define('MYSTAT_VERSION','3.0');
}
require_once(dirname(__FILE__).'/lib/mystat.class.php');
$mystat = new myStat();
$mystat->setDriver('wordpress');
$mystat->run();
