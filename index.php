<?php
/*
Plugin Name: mySTAT
Plugin URI: http://my-stat.com
Description: mySTAT is a unique product in the area of statistics, analytics and SEO optimization. mySTAT is a comprehensive set of tools necessary for collecting and classifying data from all areas. The product is a multi-platform one, so it can be installed onto both existing popular types of CMS and onto any other third-party developed sites. If you have your own web-site, you should definitely have it in your arsenal for running a successful business, blog or any other Internet project. The previous version of mySTAT has over 500,000 downloads and strictly positive reviews! mySTAT can help you with correct organization of working on any resource, help SEO optimizers, programmers, bloggers, designers, advertising agents, and later updates of this product will further expand the area of its application. Each licensed product copy owner will be able to receive all updates and get full technical support totally free of charge. mySTAT has unique capabilities for statistics and analytics. mySTAT is completely safe, it guarantees full confidentiality of your data, and it is fully configurable to suit specific purposes. Based on all the supplied information one can develop a realistic and detailed strategy for site, business, or any other project development.
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
