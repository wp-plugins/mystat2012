<?php
if(!defined('MYSTAT_VERSION') and !defined( 'WP_UNINSTALL_PLUGIN' )){
  throw new Exception('File not exist 404');
}

class wordpress{

  protected $run = false;
  protected $php = false;
  protected $context;
  protected $cookie = false;

  public function __construct($context){
    $this->context = $context;
  }

  public function getName(){
    return 'wordpress';
  }

  public function getTime(){
    return (int)current_time('timestamp');
  }

  public function getGMT(){
    return (int)get_option('gmt_offset');
  }

  public function isEngineRun(){
    if(!function_exists('register_activation_hook')){
      return 'Engine can not run without WordPress CMS';
    }
    register_activation_hook(realpath(dirname(__FILE__).'/../index.php'),array($this,'installPlugin'));
    register_deactivation_hook(realpath(dirname(__FILE__).'/../index.php'),array($this,'unstallPlugin'));
    register_uninstall_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__),Array(__CLASS__,'removePlugin'));
    add_action('plugins_loaded',Array($this,'updatePlugin'));
    add_action('wp_ajax_mystat',Array($this,'ajaxRun'));
    if($this->context->isIntallCorrect() and $this->context->isAllFileExists()){
      add_action('admin_menu',Array($this,'addMenu'));
      add_action('wp_footer',Array($this,'addHookCode'));
      add_action('wp_ajax_nopriv_mystat', Array($this,'ajaxRunPublic'));
      add_action('init', Array($this,'initWP'));
    }
    add_action('admin_notices',Array($this,'adminNotice'));
    add_action('admin_enqueue_scripts', Array($this,'adminScripts'));
    return true;
  }

  public function setUpdateStop($report=false){
//    $ret = '<script>document.location=\''.$this->getRedirectUri($report).'\';</script>';
//    return $ret;
  }

  public function setUpdateStart(){
    echo str_repeat('.',100);
    flush();
    usleep(100);
  }

  public function setRunHook($el,$func){
    $this->run = Array($func,$el);
  }

  public function getParam($name,$default=false){
    return isset($_POST[$name])?$_POST[$name]:(($name=='report' and isset($_GET[$name]))?$_GET[$name]:$default);
  }

  public function getOption($name,$default=false){
    return get_option($name,$default);
  }

  public function getUserHash(){
    if($this->cookie===false){
      if(isset($_COOKIE['mystathash']) and $_COOKIE['mystathash']!=''){
        $this->cookie = $_COOKIE['mystathash'];
      }else{
        $this->cookie = md5($_SERVER['HTTP_USER_AGENT'].(($_SERVER['REMOTE_ADDR']==$_SERVER['SERVER_ADDR'])?(isset($_SERVER['HTTP_X_REAL_IP'])?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR']):$_SERVER['REMOTE_ADDR']).rand());
      }
    }
    return $this->cookie;
  }

  public function isFeed(){
    return is_feed();
  }

  public function setOption($name,$value){
    if($value===false){
      delete_option($name);
      return $this;
    }
    update_option($name,$value);
    return $this;
  }

  public function __($text){
    return __($text,'mystat');
  }

  public function getWebPath(){
    return plugins_url('asset/', dirname(__FILE__));
  }

  public function getLanguage(){
    return strtoupper(substr(get_locale(),0,2));
  }
  

  public function is404(){
    return is_404();
  }

  public function setCodeHook($el,$func){
    $this->php = Array($func,$el);
  }

  public function setJsSend(){
    $url = admin_url('admin-ajax.php');
    $ret =  <<<JS
      var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\\+\\/\\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\\r\\n/g,"\\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
      jQuery(document).ready(function($) {
        var stat = runStatisticMyStat();
        $.ajax({
          url: '{$url}',
          data: {
            action: 'mystat',
            report: 'insert',
            data: Base64.encode(JSON.stringify(stat)),
            coding: 'base64'
          },
          dataType: 'json',
          type: 'POST',
          success: function(data, textStatus){
          },
          error: function(){
          }
        });
      });
JS;
    return $ret;
  }

  public function setStatInsert($param){
    $id = 0;
    global $wpdb;
    $rows=$wpdb->get_var(
      $wpdb->prepare('
        SELECT id FROM '.$wpdb->prefix.'mystatdata
        WHERE
          created_at>=TIMESTAMP(%s) AND
          ip=%d AND
          ua=%s AND
          hash=%s AND
          referer=%s AND
          host=%s AND
          uri=%s
        ',
        date('Y-m-d',$this->getTime()),
        $param['ip'],
        $param['ua'],
        $param['hash'],
        $param['referer']['url'],
        $param['host'],
        $param['uri']
      )
    );
    if($rows!=''){
      $id=$rows;
      $wpdb->query("UPDATE ".$wpdb->prefix."mystatdata SET count=count+1,updated_at='".date('Y-m-d H:i:s',$this->getTime())."' WHERE id=".$rows);
    }else{
      $wpdb->query(
        $wpdb->prepare('
          REPLACE INTO '.$wpdb->prefix.'mystatdata
            (hash,ua,browser,browser_version,device,time_load,ip,proxy,is404,is_feed,title,host,www,file,uri,referer,reftype,refname,refquery,lang,country,screen,depth,gzip,deflate,mobile,tablet,crawler,os,osver,osname,osbit,count,created_at,updated_at)
            VALUES
            (%s,%s,%s,%s,%s,0,%d,%d,%d,%d,\'\',%s,%d,%s,%s,%s,%s,%s,%s,%s,%s,\'\',0,%d,%d,%d,%d,%d,%s,%s,%s,%d,1,%s,%s)
          ',
          $param['hash'],
          $param['ua'],
          $param['browser'],
          $param['version'],
          $param['device'],
          $param['ip'],
          $param['proxy'],
          $param['404'],
          $param['feed'],
          $param['host'],
          $param['www'],
          $param['file'],
          $param['uri'],
          $param['referer']['url'],
          $param['referer']['type'],
          $param['referer']['name'],
          $param['referer']['query'],
          $param['lang'],
          $param['country'],
          $param['gzip'],
          $param['deflate'],
          $param['mobile'],
          $param['tablet'],
          $param['crawler'],
          $param['os'],
          $param['osver'],
          $param['osname'],
          $param['osbit'],
          date('Y-m-d H:i:s',$this->getTime()),
          date('Y-m-d H:i:s',$this->getTime())
        )
      );
      $rows=$wpdb->get_var("SELECT LAST_INSERT_ID()");
      $id=$rows;
    }
    return $id;
  }

  public function setStatUpdate($id,$param){
    global $wpdb;
    if($id>0){
      $rows=$wpdb->get_var(
        $wpdb->prepare('
          SELECT updated_at FROM '.$wpdb->prefix.'mystatdata
          WHERE
            id=%d
          ',
          $id
        )
      );
      if($rows==''){return;}
      $tload = $this->getTime()-strtotime($rows);
      $title = (string)$param['title'];unset($param['title']);
      $screen = '';
      if(isset($param['screen']['width']) and (int)$param['screen']['width']>0){
        $screen = $param['screen']['width'].'x'.$param['screen']['height'];
        $depth = $param['screen']['depth'];
        unset($param['screen']);
      }
      $rows=$wpdb->get_var(
        $wpdb->prepare('
          UPDATE '.$wpdb->prefix.'mystatdata SET
            time_load=%d,
            title=%s,
            screen=%s,
            depth=%d,
            param=%s,
            updated_at=%s
          WHERE
            id=%d
          ',
          $tload,
          $title,
          $screen,
          $depth,
          json_encode($param),
          date('Y-m-d H:i:s',$this->getTime()),
          $id
        )
      );
    }
  }

  public function getStatByPeriod($from,$to){
    global $wpdb;
    $rows = $wpdb->get_results(
      $wpdb->prepare('
        SELECT * FROM '.$wpdb->prefix.'mystatdata WHERE
          created_at>=%s AND
          created_at<=%s
        ',
        date('Y-m-d 00:00:00',$from),
        date('Y-m-d 23:59:59',$to)
      )
    );
    $ret = Array();
    foreach($rows as $r){
      $el = json_decode($r->param,true);
      $el['id'] = (int)$r->id;
      $el['hash'] = (string)$r->hash;
      $el['ua'] = (string)$r->ua;
      $el['browser'] = (string)$r->browser;
      $el['version'] = (string)$r->browser_version;
      $el['os'] = (string)$r->os;
      $el['osver'] = (string)$r->osver;
      $el['osname'] = (string)$r->osname;
      $el['osbit'] = (int)$r->osbit;
      $el['crawler'] = (bool)$r->crawler;
      $el['mobile'] = (bool)$r->mobile;
      $el['tablet'] = (bool)$r->tablet;
      $el['device'] = (string)$r->device;
      $el['ip'] = (int)$r->ip;
      $el['country'] = strtoupper((string)$r->country);
      $el['www'] = (bool)$r->www;
      $el['host'] = (string)$r->host;
      $el['lang'] = strtoupper((string)$r->lang);
      $el['uri'] = (string)$r->uri;
      $el['file'] = (string)$r->file;
      $el['gzip'] = (bool)$r->gzip;
      $el['deflate'] = (bool)$r->deflate;
      $el['proxy'] = (bool)$r->proxy;
      $el['referer'] = Array(
        'url' => (string)$r->referer,
        'type' => (string)$r->reftype,
        'name' => (string)$r->refname,
        'query' => (string)$r->refquery
      );
      $el['404'] = (bool)$r->is404;
      $el['feed'] = (bool)$r->is_feed;
      $el['title'] = (string)$r->title;
      $screen = (string)$r->screen;
      $screen = preg_split('/x/',$screen);
      $el['screen'] = Array(
        'width' => isset($screen[0])?(int)$screen[0]:0,
        'height' => isset($screen[1])?(int)$screen[1]:0,
        'depth' => (int)$r->depth
      );
      $el['count'] = (int)$r->count;
      $el['created_at'] = strtotime($r->created_at);
      $el['updated_at'] = strtotime($r->updated_at);
      $ret[] = $el;
    }
    return $ret;
  }

##############################################################

  public function addHookCode(){
    call_user_func(array_shift($this->php),array_shift($this->php));
  }

  public function installPlugin(){
    if(!current_user_can('activate_plugins')){return;}
    $plugin = isset($_REQUEST['plugin'])?$_REQUEST['plugin']:'';
    check_admin_referer('activate-plugin_'.$plugin);

    global $wpdb;

    $charset_collate = '';
    if(!empty($wpdb->charset)){
      $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset;
    }
    if(!empty( $wpdb->collate ) ) {
      $charset_collate.= ' COLLATE '.$wpdb->collate;
    }
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix.'mystatdata';
    $sql = 'CREATE TABLE '.$table_name.' ('."\n".
      'id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,'."\n".
      'hash varchar(32),'."\n".
      'ua text,'."\n".
      'browser varchar(200),'."\n".
      'browser_version varchar(10),'."\n".
      'device varchar(200),'."\n".
      'time_load int(11) UNSIGNED,'."\n".
      'ip bigint,'."\n".
      'proxy bool,'."\n".
      'is404 bool,'."\n".
      'is_feed bool,'."\n".
      'title text,'."\n".
      'host varchar(200),'."\n".
      'www bool,'."\n".
      'file varchar(200),'."\n".
      'uri text,'."\n".
      'referer text,'."\n".
      'lang char(2),'."\n".
      'reftype varchar(50),'."\n".
      'refname varchar(50),'."\n".
      'refquery text,'."\n".
      'country char(2),'."\n".
      'screen varchar(12),'."\n".
      'depth smallint,'."\n".
      'gzip bool,'."\n".
      'deflate bool,'."\n".
      'mobile bool,'."\n".
      'tablet bool,'."\n".
      'crawler bool,'."\n".
      'os varchar(50),'."\n".
      'osver varchar(10),'."\n".
      'osname varchar(250),'."\n".
      'osbit tinyint,'."\n".
      'count int(11) UNSIGNED,'."\n".
      'param longtext,'."\n".
      'created_at timestamp NOT NULL default \'0000-00-00 00:00:00\','."\n".
      'updated_at timestamp NOT NULL default \'0000-00-00 00:00:00\','."\n".
      'UNIQUE KEY id (id)'."\n".
    ') '.$charset_collate.';';
    dbDelta($sql);
    $table_name = $wpdb->prefix.'mystatsize';
    $sql='CREATE TABLE '.$table_name.' ('."\n".
      'date date,'."\n".
      'size int(11) unsigned,'."\n".
      'PRIMARY KEY  (date)'."\n".
    ') '.$charset_collate.';';
    dbDelta($sql);
    $this->setOption('mystatversion',MYSTAT_VERSION);
  }

  public function unstallPlugin(){
    if(!current_user_can('activate_plugins')){return;}
    $plugin = isset($_REQUEST['plugin'])?$_REQUEST['plugin']:'';
    check_admin_referer('deactivate-plugin_'.$plugin);

    global $wpdb;
    $wpdb->query('DROP TABLE '.$wpdb->prefix.'mystatdata;');
    $wpdb->query('DROP TABLE '.$wpdb->prefix.'mystatsize;');
  }
  
  public static function removePlugin(){
    if(!current_user_can('activate_plugins') or __FILE__ != WP_UNINSTALL_PLUGIN){return;}
    check_admin_referer('bulk-plugins');
    $f = fopen(realpath(dirname(__FILE__).'/../../log.log'),'a+');
    fwrite($f,'REMOVE'."\n");
    fclose($f);
  }

  public function updatePlugin(){
    load_plugin_textdomain('mystat',false,dirname(plugin_basename(realpath(dirname(__FILE__).'/../index.php'))).'/language');
    if($this->getOption('mystatversion') != MYSTAT_VERSION){
      $this->installPlugin();
    }
  }

  public function addMenu(){
    add_menu_page($this->__('My Stats'),$this->__('My Stats'),'update_plugins','statistics.html',Array($this,'setOpenPage'),'dashicons-chart-bar',4);
  }

  public function setOpenPage($ajax = false){
    if($this->run===false){return;}
    echo !$ajax?'<div id="mystat">':'';
    call_user_func(array_shift($this->run),array_shift($this->run));
    echo !$ajax?'</div>':'';
  }

  public function ajaxRun(){
    $this->setOpenPage(true);
    exit;
  }

  public function ajaxRunPublic(){
    $page = (string)$this->getParam('report','dashboard');
    if(in_array($page,Array('insert'))){
      $this->setOpenPage(true);
      echo '{"success":true}';
      exit;
    }
    echo '{"success":false}';
    exit;
  }
  
  public function adminNotice(){
    if(!$this->context->isIntallCorrect()){
      echo '<div class="error">';
      echo '<p><strong>'.$this->__('My Stats').':</strong> '.$this->__('Plugin has no permissions to write to the directory "cache". Plugin can not independently resolve this error. Contact your administrator.').'</p>';
      echo '</div>';
      return false;
    }
    if($this->context->isNeedUpdate()){
      echo '<div class="update-nag">';
      echo '<strong>'.$this->__('My Stats').':</strong> '.$this->__('Need to update definitions');
      echo '<a id="update_mystat" style="margin-left: 10px;margin-top:-3px;margin-bottom:-3px;" class="button button-small button-primary" onclick="if(!jQuery(\'#update_mystat\').hasClass(\'button-primary\')){return false;};jQuery(\'#update_mystat\').removeClass(\'button-primary\');jQuery(\'#update_mystat .spinner\').show();jQuery.ajax({url: ajaxurl,data: {action: \'mystat\',report: \'update\'},timeout: 300000, dataType: \'html\',type: \'POST\',success: function(data, textStatus){document.location=\''.$this->getRedirectUri().'\';},error: function(){alert(\''.$this->__('Произошла ошибка при обновлении, повторите попытку позже.').'\');jQuery(\'#update_mystat\').addClass(\'button-primary\');jQuery(\'#update_mystat .spinner\').hide();}});return false;">'.$this->__('Обновить').' <span class="spinner"></span></a>';
      echo '</div>';
    }
  }

  public function adminScripts(){
    $webpath = $this->getWebPath();
    wp_register_script('moment_js', trim($webpath,'/').'/moment.min.js', Array('jquery-core'), '2.8.3' );
    wp_enqueue_script('moment_js');
    wp_register_script('daterangepicker_js', trim($webpath,'/').'/jquery.daterangepicker.min.js', Array('jquery-core','moment_js'), '0.0.5' );
    wp_enqueue_script('daterangepicker_js');
    wp_register_style('daterangepicker_css', trim($webpath,'/').'/jquery.daterangepicker.min.css', false, '0.0.5' );
    wp_enqueue_style('daterangepicker_css');
  }

  public function initWP(){
    if(!is_admin()){
      $cookie = '';
      if(isset($_COOKIE['mystathash']) and $_COOKIE['mystathash']!=''){
        $cookie = $_COOKIE['mystathash'];
      }
      if($cookie==''){
        $cookie = $this->getUserHash();
      }
      setcookie('mystathash',$cookie,$this->getTime()+(60*60*24*365),COOKIEPATH, COOKIE_DOMAIN);
    }
  }

  private function getRedirectUri($report=false){
    return menu_page_url(plugin_basename('statistics.html'),false).($report!==false?'&report='.$report:'');
  }

}
