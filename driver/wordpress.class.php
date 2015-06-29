<?php
if(!defined('MYSTAT_VERSION')){
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

  public function getTime($no_gmt=false){
    return (float)current_time('timestamp',$no_gmt);
  }

  public function getGMT(){
    return (int)get_option('gmt_offset');
  }

  public function isAjax(){
    return $this->getParam('ajax','false')=='false'?false:true;
  }

  public function isEngineRun(){
    if(!function_exists('register_activation_hook')){
      return 'Driver can not run without WordPress CMS';
    }
    return true;
  }

  public function startDriver(){
    register_activation_hook(realpath(dirname(__FILE__).'/../index.php'),array($this,'installPlugin'));
    register_deactivation_hook(realpath(dirname(__FILE__).'/../index.php'),array($this,'unstallPlugin'));
    register_uninstall_hook(realpath(dirname(__FILE__).'/../index.php'),Array(__CLASS__,'removePlugin'));
    add_action('plugins_loaded',Array($this,'updatePlugin'));
    add_action('wp_ajax_mystat',Array($this,'ajaxRun'));
    add_action('admin_menu',Array($this,'addMenu'));
    if($this->context->isIntallCorrect() and $this->context->isAllFileExists()){
      add_action('wp_head',Array($this,'addHeaderCode'));
      add_action('wp_footer',Array($this,'addHookCode'));
      add_action('wp_ajax_nopriv_mystat', Array($this,'ajaxRunPublic'));
      add_action('init', Array($this,'initWP'));
    }
    add_action('admin_notices',Array($this,'adminNotice'));
    add_action('admin_enqueue_scripts', Array($this,'adminScripts'));

    add_action('restrict_manage_users', Array($this,'addWPStatUser'));
    add_action('restrict_manage_posts', Array($this,'addWPStatPost'));
    add_action('manage_comments_nav', Array($this,'addWPStatComm'));
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

  public function setOption($name,$value=false){
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
        date('Y-m-d',$this->getTime(false)),
        $param['ip'],
        $param['ua'],
        $param['hash'],
        $param['referer']['url'],
        $param['host'],
        $param['uri']
      )
    );
    $timer = microtime(true);
    if($rows!=''){
      $id=$rows;
      $wpdb->query("UPDATE ".$wpdb->prefix."mystatdata SET time_start=".(($timer-floor($timer))*10000).",count=count+1,updated_at='".date('Y-m-d H:i:s',$this->getTime(false))."' WHERE id=".$rows);
    }else{
      $r = $wpdb->replace(
        $wpdb->prefix.'mystatdata',
        Array(
          'time_start' => ($timer-floor($timer))*10000,
          'hash' => $param['hash'],
          'ua' => $param['ua'],
          'browser' => $param['browser'],
          'browser_version' => $param['version'],
          'device' => $param['device'],
          'time_load' => 0,
          'ip' => $param['ip'],
          'proxy' => $param['proxy'],
          'is404' => $param['404'],
          'is_feed' => $param['feed'],
          'title' => '',
          'host' => $param['host'],
          'www' => $param['www'],
          'file' => $param['file'],
          'uri' => $param['uri'],
          'referer' => $param['referer']['url'],
          'reftype' => $param['referer']['type'],
          'refname' => $param['referer']['name'],
          'refquery' => $param['referer']['query'],
          'lang' => $param['lang'],
          'country' => $param['country'],
          'screen' => '',
          'depth' => 0,
          'gzip' => $param['gzip'],
          'deflate' => $param['deflate'],
          'mobile' => $param['mobile'],
          'tablet' => $param['tablet'],
          'crawler' => $param['crawler'],
          'os' => $param['os'],
          'osver' => $param['osver'],
          'osname' => $param['osname'],
          'osbit' => $param['osbit'],
          'count' => 1,
          'created_at' => date('Y-m-d H:i:s',$this->getTime(false)),
          'updated_at' => date('Y-m-d H:i:s',$this->getTime(false))
        ),
        Array('%d','%s','%s','%s','%s','%s','%d','%d','%d','%d','%d','%s','%s','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%d','%d','%d','%d','%d','%s','%s','%s','%d','%d','%s','%s')
      );
      if($r>0){
        $id=$wpdb->insert_id;
      }
    }
    return $id;
  }

  public function setStatUpdate($id,$param){
    global $wpdb;
    if($id>0){
      $timer = microtime(true);
      $rows=$wpdb->get_results(
        $wpdb->prepare('
          SELECT updated_at,time_start FROM '.$wpdb->prefix.'mystatdata
          WHERE
            id=%d
          ',
          $id
        )
      );
      if(sizeof($rows)==0){return;}
      $tload = ($this->getTime(false)+($rows[0]->time_start/10000))-(strtotime($rows[0]->updated_at)+($timer-floor($timer)));

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
            time_load=%f,
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
          date('Y-m-d H:i:s',$this->getTime(false)),
          $id
        )
      );
    }
  }

  public function getStatByPeriod($from,$to){
    global $wpdb;
    $query = $wpdb->prepare('
      SELECT * FROM '.$wpdb->prefix.'mystatdata WHERE
        created_at>=%s AND
        created_at<=%s
      ',
      date('Y-m-d 00:00:00',$from),
      date('Y-m-d 23:59:59',$to)
    );
  	if($wpdb->use_mysqli){
			$result = @mysqli_query($wpdb->dbh,$query,MYSQLI_USE_RESULT);
		}else{
			$result = @mysql_query($query,$wpdb->dbh);
		}
    if(!$result){return Array();}
    return new dbResult($result);
  }

  public function getDbSizeByPeriod($from,$to){
    global $wpdb;
    $query = $wpdb->get_results($wpdb->prepare('
      SELECT * FROM '.$wpdb->prefix.'mystatsize WHERE
        date>=%s AND
        date<=%s
      ',
      date('Y-m-d 00:00:00',$from),
      date('Y-m-d 23:59:59',$to)
    ),ARRAY_A);
    if(!$query){return Array();}
    return $query;
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
      'time_start int(11) UNSIGNED,'."\n".
      'time_load float(9,4) UNSIGNED,'."\n".
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
    $table_name = $wpdb->prefix.'mystatclick';
    $sql='CREATE TABLE '.$table_name.' ('."\n".
      'x smallint(6) UNSIGNED,'."\n".
      'y smallint(6) UNSIGNED,'."\n".
      'uri text,'."\n".
      'created_at timestamp NOT NULL default \'0000-00-00 00:00:00\''."\n".
    ') '.$charset_collate.';';
    dbDelta($sql);
    $table_name = $wpdb->prefix.'mystatsize';
    $sql='CREATE TABLE '.$table_name.' ('."\n".
      'date date,'."\n".
      'size int(11) unsigned'."\n".
    ') '.$charset_collate.';';
    dbDelta($sql);
    $this->setOption('mystatversion',MYSTAT_VERSION);
    $this->setOption('mystat');
    $this->setOption('mystatlastupdate');
  }

  public function unstallPlugin(){
    if(!current_user_can('activate_plugins')){return;}
    $plugin = isset($_REQUEST['plugin'])?$_REQUEST['plugin']:'';
    check_admin_referer('deactivate-plugin_'.$plugin);
    $this->setOption('mystatversion');
    $this->setOption('mystat');
    $this->setOption('mystatlastupdate');
  }
  
  public static function removePlugin(){
    if(!current_user_can('activate_plugins')){return;}
    check_admin_referer('bulk-plugins');
    global $wpdb;
    $wpdb->query('DROP TABLE '.$wpdb->prefix.'mystatdata;');
    $wpdb->query('DROP TABLE '.$wpdb->prefix.'mystatclick;');
    $wpdb->query('DROP TABLE '.$wpdb->prefix.'mystatsize;');
    $this->setOption('mystatuuid');
  }

  public function updatePlugin(){
    load_plugin_textdomain('mystat',false,dirname(plugin_basename(realpath(dirname(__FILE__).'/../index.php'))).'/language');
    if($this->getOption('mystatversion') != MYSTAT_VERSION){
      $this->installPlugin();
    }
  }

  public function addMenu(){
    add_menu_page($this->__('My Statistics'),$this->__('My Statistics'),'update_plugins','statistics.html',Array($this,'setOpenPage'),'dashicons-chart-bar',4);
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
      echo '<p><strong>'.$this->__('My Statistics').':</strong> '.$this->__('Plugin has no permissions to write to the directory "cache". Plugin can not independently resolve this error. Contact your administrator.').'</p>';
      echo '</div>';
      return false;
    }
    if($this->context->isNeedUpdate()){
      echo '<div class="update-nag">';
      echo '<strong>'.$this->__('My Statistics').':</strong> '.$this->__('Need to update definitions');
      echo '<a id="update_mystat" style="margin-left: 10px;margin-top:-3px;margin-bottom:-3px;" class="button button-small button-primary" onclick="if(!jQuery(\'#update_mystat\').hasClass(\'button-primary\')){return false;};jQuery(\'#update_mystat\').removeClass(\'button-primary\');jQuery(\'#update_mystat .spinner\').show();jQuery.ajax({url: ajaxurl,data: {action: \'mystat\',report: \'update\'},timeout: 300000, dataType: \'html\',type: \'POST\',success: function(data, textStatus){document.location=\''.$this->getRedirectUri().'\';},error: function(){alert(\''.$this->__('An error occurred during the update, please, try again later.').'\');jQuery(\'#update_mystat\').addClass(\'button-primary\');jQuery(\'#update_mystat .spinner\').hide();}});return false;">'.$this->__('Update').' <span class="spinner" style="visibility: visible;display: none;margin: 1px 10px 0;"></span></a>';
      echo '</div>';
    }
  }

  public function adminScripts(){
    $webpath = $this->getWebPath();
    wp_register_script('google_js', 'https://www.google.com/jsapi');
    wp_enqueue_script('google_js');
    wp_register_script('mystatlogo_js', trim($webpath,'/').'/logo.min.js',false,'0.3.0' );
    wp_enqueue_script('mystatlogo_js');
    wp_register_script('moment_js', trim($webpath,'/').'/moment.min.js', Array('jquery-core'), '2.9.0' );
    wp_enqueue_script('moment_js');
    wp_register_script('maskedinput_js', trim($webpath,'/').'/jquery.maskedinput.min.js', Array('jquery-core'), '1.4.0' );
    wp_enqueue_script('maskedinput_js');
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
      setcookie('mystathash',$cookie,$this->getTime(false)+(60*60*24*365),COOKIEPATH, COOKIE_DOMAIN);
    }
  }

  private function getRedirectUri($report=false){
    return menu_page_url(plugin_basename('statistics.html'),false).($report!==false?'&report='.$report:'');
  }

  public function addWPStatUser(){
    $user_query = new WP_User_Query(array('orderby'=>'registered', 'order'=>'ASC'));
    $users = (array)$user_query->results;
    $arr = $arr30 = Array();
    for($i=29;$i>=0;$i--){
      $arr30[date('Y-m-d',strtotime(date('Y-m-d',$this->getTime(true)).' 00:00:00 -'.$i.'days'))] = 0;
    }
    foreach($users as $u){
      $date = date('Y-m-d',strtotime($u->user_registered));
      if(isset($arr30[$date])){
        $arr30[$date]++;
      }
      if(isset($arr[$date])){
        $arr[$date]++;
      }else{
        $arr[$date] = 1;
      }
    }
    $json30 = $jsonall = Array();
    foreach($arr30 as $date=>$count){
      $json30[] = '[new Date('.strtotime($date.' 00:00:00').' * 1000),'.$count.']';
    }
    $json30 = join(',',$json30);
    foreach($arr as $date=>$count){
      $json[] = '[new Date('.strtotime($date.' 00:00:00').' * 1000),'.$count.']';
    }
    $json = join(',',$json);
    ?>
    <style>
      #mystat_graphic{clear:both;margin: 20px;}
      #mystat_title{float:left;font-size:16px;margin:10px 50px;}
      #mystat_button{float:right;margin:10px;}
    </style>
    <script>
      if(jQuery("#mystat_graphic").length==0){
        var json30 = [<?php echo $json30;?>];
        var jsonall = [<?php echo $json;?>];
        jQuery(".wrap h2").after("<div class='postbox'><div id='mystat_title'><?php echo addslashes($this->__('User registrations'));?></div><div id='mystat_button'><a class='button button-small button-primary' onclick='mystatChartReload(false);'><?php echo addslashes($this->__('Within 30 days'));?></a> <a class='button button-small' onclick='mystatChartReload(true);'><?php echo addslashes($this->__('Throughout the whole period'));?></a></div><div id='mystat_graphic'></div></div>");
        if(typeof google != 'undefined' && typeof google.visualization == 'undefined'){
          google.load('visualization', '1.0', {'callback':function(){},'packages':['corechart'], 'language':'<?php echo $this->getLanguage();?>'});
          google.setOnLoadCallback(viewChart);
        }
        var chart = null;
        var data = null;
        var options = {
          height: 150,
          legend: {
            position: 'labeled'
          },
          vAxis: {
            format: '#'
          },
          pieHole: 0.4,
          dataOpacity: 0.9,
          theme: 'maximized',
          focusTarget: 'category'
        };
        function viewChart(){
          if(typeof google == 'undefined' || typeof google.visualization == 'undefined' || typeof google.visualization.DataTable == 'undefined'){return;}
          data = new google.visualization.DataTable();
          data.addColumn('date', '');
          data.addColumn('number', "<?php echo addslashes($this->__('Users registered'));?>");
          data.addRows(json30);
          chart = new google.visualization.ColumnChart(document.getElementById('mystat_graphic'));
          chart.draw(data, options);
        }
        function mystatChartReload(all){
          console.info(jQuery('#mystat_button a'));
          jQuery('#mystat_button a').eq(all?0:1).removeClass('button-primary');
          jQuery('#mystat_button a').eq(all?1:0).addClass('button-primary');
          data.removeRows(0,all?json30.length:jsonall.length);
          data.addRows(all?jsonall:json30);
          chart.draw(data,options);
        }
       }
    </script>
    <?php
  }

  public function addWPStatPost(){
    $posts = get_posts();
    $arr = $arr30 = Array();
    for($i=29;$i>=0;$i--){
      $arr30[date('Y-m-d',strtotime(date('Y-m-d',$this->getTime(true)).' 00:00:00 -'.$i.'days'))] = 0;
    }
    foreach($posts as $p){
      $date = date('Y-m-d',strtotime($p->post_date));
      if(isset($arr30[$date])){
        $arr30[$date]++;
      }
      if(isset($arr[$date])){
        $arr[$date]++;
      }else{
        $arr[$date] = 1;
      }
    }
    $json30 = $jsonall = Array();
    foreach($arr30 as $date=>$count){
      $json30[] = '[new Date('.strtotime($date.' 00:00:00').' * 1000),'.$count.']';
    }
    $json30 = join(',',$json30);
    foreach($arr as $date=>$count){
      $json[] = '[new Date('.strtotime($date.' 00:00:00').' * 1000),'.$count.']';
    }
    $json = join(',',$json);
    ?>
    <style>
      #mystat_graphic{clear:both;margin: 20px;}
      #mystat_title{float:left;font-size:16px;margin:10px 50px;}
      #mystat_button{float:right;margin:10px;}
    </style>
    <script>
      if(jQuery("#mystat_graphic").length==0){
        var json30 = [<?php echo $json30;?>];
        var jsonall = [<?php echo $json;?>];
        jQuery(".wrap h2").after("<div class='postbox'><div id='mystat_title'><?php echo addslashes($this->__('User posts'));?></div><div id='mystat_button'><a class='button button-small button-primary' onclick='mystatChartReload(false);'><?php echo addslashes($this->__('Within 30 days'));?></a> <a class='button button-small' onclick='mystatChartReload(true);'><?php echo addslashes($this->__('Throughout the whole period'));?></a></div><div id='mystat_graphic'></div></div>");
        if(typeof google != 'undefined' && typeof google.visualization == 'undefined'){
          google.load('visualization', '1.0', {'callback':function(){},'packages':['corechart'], 'language':'<?php echo $this->getLanguage();?>'});
          google.setOnLoadCallback(viewChart);
        }
        var chart = null;
        var data = null;
        var options = {
          height: 150,
          legend: {
            position: 'labeled'
          },
          vAxis: {
            format: '#'
          },
          pieHole: 0.4,
          dataOpacity: 0.9,
          theme: 'maximized',
          focusTarget: 'category'
        };
        function viewChart(){
          if(typeof google == 'undefined' || typeof google.visualization == 'undefined' || typeof google.visualization.DataTable == 'undefined'){return;}
          data = new google.visualization.DataTable();
          data.addColumn('date', '');
          data.addColumn('number', "<?php echo addslashes($this->__('User posts'));?>");
          data.addRows(json30);
          chart = new google.visualization.ColumnChart(document.getElementById('mystat_graphic'));
          chart.draw(data, options);
        }
        function mystatChartReload(all){
          console.info(jQuery('#mystat_button a'));
          jQuery('#mystat_button a').eq(all?0:1).removeClass('button-primary');
          jQuery('#mystat_button a').eq(all?1:0).addClass('button-primary');
          data.removeRows(0,all?json30.length:jsonall.length);
          data.addRows(all?jsonall:json30);
          chart.draw(data,options);
        }
       }
    </script>
    <?php
  }

  public function addWPStatComm(){
    $comment = get_comments();
    $arr = $arr30 = Array();
    for($i=29;$i>=0;$i--){
      $arr30[date('Y-m-d',strtotime(date('Y-m-d',$this->getTime(true)).' 00:00:00 -'.$i.'days'))] = 0;
    }
    foreach($comment as $c){
      $date = date('Y-m-d',strtotime($c->comment_date));
      if(isset($arr30[$date])){
        $arr30[$date]++;
      }
      if(isset($arr[$date])){
        $arr[$date]++;
      }else{
        $arr[$date] = 1;
      }
    }
    $json30 = $jsonall = Array();
    foreach($arr30 as $date=>$count){
      $json30[] = '[new Date('.strtotime($date.' 00:00:00').' * 1000),'.$count.']';
    }
    $json30 = join(',',$json30);
    foreach($arr as $date=>$count){
      $jsonall[] = '[new Date('.strtotime($date.' 00:00:00').' * 1000),'.$count.']';
    }
    $jsonall = join(',',$jsonall);
    ?>
    <style>
      #mystat_graphic{clear:both;margin: 20px;}
      #mystat_title{float:left;font-size:16px;margin:10px 50px;}
      #mystat_button{float:right;margin:10px;}
    </style>
    <script>
      if(jQuery("#mystat_graphic").length==0){
        var json30 = [<?php echo $json30;?>];
        var jsonall = [<?php echo $jsonall;?>];
        jQuery(".wrap h2").after("<div class='postbox'><div id='mystat_title'><?php echo addslashes($this->__('User comments'));?></div><div id='mystat_button'><a class='button button-small button-primary' onclick='mystatChartReload(false);'><?php echo addslashes($this->__('Within 30 days'));?></a> <a class='button button-small' onclick='mystatChartReload(true);'><?php echo addslashes($this->__('Throughout the whole period'));?></a></div><div id='mystat_graphic'></div></div>");
        if(typeof google != 'undefined' && typeof google.visualization == 'undefined'){
          google.load('visualization', '1.0', {'callback':function(){},'packages':['corechart'], 'language':'<?php echo $this->getLanguage();?>'});
          google.setOnLoadCallback(viewChart);
        }
        var chart = null;
        var data = null;
        var options = {
          height: 150,
          legend: {
            position: 'labeled'
          },
          vAxis: {
            format: '#'
          },
          pieHole: 0.4,
          dataOpacity: 0.9,
          theme: 'maximized',
          focusTarget: 'category'
        };
        function viewChart(){
          if(typeof google == 'undefined' || typeof google.visualization == 'undefined' || typeof google.visualization.DataTable == 'undefined'){return;}
          data = new google.visualization.DataTable();
          data.addColumn('date', '');
          data.addColumn('number', "<?php echo addslashes($this->__('User comments'));?>");
          data.addRows(json30);
          chart = new google.visualization.ColumnChart(document.getElementById('mystat_graphic'));
          chart.draw(data, options);
        }
        function mystatChartReload(all){
          console.info(jQuery('#mystat_button a'));
          jQuery('#mystat_button a').eq(all?0:1).removeClass('button-primary');
          jQuery('#mystat_button a').eq(all?1:0).addClass('button-primary');
          data.removeRows(0,all?json30.length:jsonall.length);
          data.addRows(all?jsonall:json30);
          chart.draw(data,options);
        }
       }
    </script>
    <?php
  }

  public function dbSizeCollect(){
    global $wpdb;
    $days = (int)$this->getOption('mystatcleanday',120);
    $days = $days>30?$days:30;
    $wpdb->query('DELETE FROM '.$wpdb->prefix.'mystatdata WHERE created_at<=TIMESTAMP("'.date('Y-m-d 00:00:00',strtotime(date('Y-m-d',$this->getTime(false)).' -'.$days.' days')).'")');
    $wpdb->query('DELETE FROM '.$wpdb->prefix.'mystatclick WHERE created_at<=TIMESTAMP("'.date('Y-m-d 00:00:00',strtotime(date('Y-m-d',$this->getTime(false)).' -'.$days.' days')).'")');
    $wpdb->query('DELETE FROM '.$wpdb->prefix.'mystatsize WHERE date<=TIMESTAMP("'.date('Y-m-d 00:00:00',strtotime(date('Y-m-d',$this->getTime(false)).' -'.$days.' days')).'")');
    $query = $wpdb->get_results('SHOW TABLE STATUS LIKE \''.$wpdb->prefix.'mystat%\'',ARRAY_A);
    $size = 0;
    foreach($query as $el){
      $size+= $el['Data_length'] + $el['Index_length'];
    }
    $exist = (int)$wpdb->get_var($wpdb->prepare('
		  SELECT count(*)
		  FROM '.$wpdb->prefix.'mystatsize
		  WHERE date = %s', 
	    date('Y-m-d',$this->getTime(false))
    ));
    if($exist==0){
      $wpdb->insert(
        $wpdb->prefix.'mystatsize',
        Array(
          'date' => date('Y-m-d',$this->getTime(false)),
          'size' => $size
        ),
        Array('%s','%f')
      );
    }else{
      $wpdb->update(
        $wpdb->prefix.'mystatsize',
        Array(
          'size' => $size
        ),
        Array(
          'date' => date('Y-m-d',$this->getTime(false))
        ),
        Array('%f'),
        Array('%s')
      );
    }
  }

  public function addHeaderCode(){
    echo '<meta name="referrer" content="origin" />';
    $this->dbSizeCollect();
  }

}

class dbResult implements Iterator{

  private $link = null;
  private $row = null;
  private $count = 0;
  private $position = 0;

  public function __construct(&$link){
    global $wpdb;
    $this->link = $link;
//  	if($wpdb->use_mysqli){
//			$count = (int)@mysqli_num_rows($this->link);
//		}else{
//			$count = (int)@mysql_num_rows($this->link);
//		}
//    $this->count = $count;
  }

  function rewind(){
  }

  function current(){
    global $wpdb;
    $el = json_decode($this->row->param,true);
    $el['time_load'] = (float)$this->row->time_load;
    $el['id'] = (int)$this->row->id;
    $el['hash'] = (string)$this->row->hash;
    $el['ua'] = (string)$this->row->ua;
    $el['browser'] = (string)$this->row->browser;
    $el['version'] = (string)$this->row->browser_version;
    $el['os'] = (string)$this->row->os;
    $el['osver'] = (string)$this->row->osver;
    $el['osname'] = (string)$this->row->osname;
    $el['osbit'] = (int)$this->row->osbit;
    $el['crawler'] = (bool)$this->row->crawler;
    $el['mobile'] = (bool)$this->row->mobile;
    $el['tablet'] = (bool)$this->row->tablet;
    $el['device'] = (string)$this->row->device;
    $el['ip'] = (float)$this->row->ip;
    $el['country'] = strtoupper((string)$this->row->country);
    $el['www'] = (bool)$this->row->www;
    $el['host'] = (string)$this->row->host;
    $el['lang'] = strtoupper((string)$this->row->lang);
    $el['uri'] = (string)$this->row->uri;
    $el['file'] = (string)$this->row->file;
    $el['gzip'] = (bool)$this->row->gzip;
    $el['deflate'] = (bool)$this->row->deflate;
    $el['proxy'] = (bool)$this->row->proxy;
    $el['referer'] = Array(
      'url' => (string)$this->row->referer,
      'type' => (string)$this->row->reftype,
      'name' => (string)$this->row->refname,
      'query' => (string)$this->row->refquery
    );
    $el['404'] = (bool)$this->row->is404;
    $el['feed'] = (bool)$this->row->is_feed;
    $el['title'] = (string)$this->row->title;
    $screen = (string)$this->row->screen;
    $screen = preg_split('/x/',$screen);
    $el['screen'] = Array(
      'width' => isset($screen[0])?(int)$screen[0]:0,
      'height' => isset($screen[1])?(int)$screen[1]:0,
      'depth' => (int)$this->row->depth
    );
    $el['count'] = (int)$this->row->count;
    $el['created_at'] = strtotime($this->row->created_at);
    $el['updated_at'] = strtotime($this->row->updated_at);
    return $el;
  }

  function key(){
    return $this->position;
  }

  function next(){
    $this->row = null;
    ++$this->position;
  }

  function valid(){
    global $wpdb;
    if($wpdb->use_mysqli){
      $r = mysqli_fetch_object($this->link);
    }else{
      $r = mysql_fetch_object($this->link);
    }
    $this->row = $r;
    if($this->row!=null){
//    if($this->position<$this->count){
      return true;
    }
    if($wpdb->use_mysqli){
      mysqli_free_result($this->link);
    }else{
      mysql_free_result($this->link);
    }
    return false;
  }

}
