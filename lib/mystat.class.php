<?php
if(!defined('MYSTAT_VERSION')){
  throw new Exception('File not exist 404');
}

class myStat{

  protected $engine = false;

  public function getEngine(){
    return $this->engine;
  }

  public function setEngine($engine){
    if(!preg_match('/^[A-z0-9]{1,}$/',$engine) or !file_exists(dirname(__FILE__).'/../engine/'.$engine.'.class.php')){
      throw new Exception('Wrong ENGINE param in setEngine()');
    }
    require_once(dirname(__FILE__).'/../engine/'.$engine.'.class.php');
    $this->engine = new $engine($this);
    if(true !== $error = $this->getEngine()->isEngineRun()){
      throw new Exception('ENGINE START ERROR: '.$error);
    }
    $this->getEngine()->setCodeHook($this,function($mystat){
      $id = $mystat->setStatisticFirst();
      if(!$mystat->getEngine()->isFeed()){
        echo $mystat->getJsCode($id);
      }
    });
    return $this;
  }

  public function run(){
    if($this->getEngine()===false){
      throw new Exception('Set ENGINE before run run()');
    }
    $this->getEngine()->setRunHook($this,function($mystat){
      echo $mystat->getReportPage();
    });
  }

  public function getReportPage(){
    $page = (string)$this->getEngine()->getParam('report','dashboard');
    if($page=='update'){
      if($this->getOption('mystatlastupdate')==date('dmY',$this->getEngine()->getTime())){
        return false;
      }
      if(file_exists(dirname(__FILE__).'/../cache/browscap.lock')){
        unlink(dirname(__FILE__).'/../cache/browscap.lock');
      }
      $ret = $this->getEngine()->setUpdateStart();
      require_once(dirname(__FILE__).'/browscap.class.php');
      $browscap = new browscap(dirname(__FILE__).'/../cache/');
      $browscap->doAutoUpdate = true;
      $browscap->getBrowser(null,true);
      $db_md5 = file_get_contents('http://tabgeo.com/api/v4/country/db/md5/');
      $db_content = file_get_contents('http://tabgeo.com/api/v4/country/db/get/');
      if($db_md5 == md5($db_content)){
        file_put_contents(dirname(__FILE__).'/../cache/tabgeo.dat', $db_content);
      }
      require_once(dirname(__FILE__).'/referer.class.php');
      $req = new referer();
      $req->setCache(dirname(__FILE__).'/../cache/');
      $req->update();
      $this->setOption('mystatlastupdate',date('dmY',$this->getEngine()->getTime()));
      return $ret.$this->getEngine()->setUpdateStop();
    }elseif($page=='insert'){
      $this->setStatisticSecond();
      return;
    }
    if(!preg_match('/^[A-z0-9]{1,}$/',$page) or !file_exists(dirname(__FILE__).'/../report/'.$page.'.class.php')){
      throw new Exception('No report found');
    }
    $xml1 = $this->getDefaultXML($page);
    $xml2 = $this->getXMLPage($page);
    require_once(dirname(__FILE__).'/mergexml.class.php');
    $mergexml = new mergexml(Array('updn'=>true));
    $mergexml->AddSource($xml1);
    $mergexml->AddSource($xml2);
    $xml = $mergexml->Get(1);
    return $this->getXSLTranform($page,$xml);
  }

  protected function getDefaultXML($page='dashboard'){
    $period = $this->getPeriod();
    $report = Array();
    $menu = $this->getMenu();
    $report['REPORT'] = Array(
      'PERIOD' => Array(
        'START' => date('d.m.Y',$period['start']),
        'END' => date('d.m.Y',$period['end'])
      ),
      'PATHTOASSET' => $this->getPathAsset(),
      'REPORT' => $page,
      'TRANSLATE' => Array(
        'PERIODREPORT' => $this->__('Период отображения отчёта'),
      ),
      'GMT' => (int)$this->getEngine()->getGMT(),
      'TIME' => (int)$this->getEngine()->getTime(),
      'LANGUAGE' => $this->getEngine()->getLanguage(),
      'MENU' => $menu
    );
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    $xml->preserveWhiteSpace = false;
    $this->xmlStructureFromArray($xml,$report);
    return $xml->saveXML();
  }

  protected function getMenu(){
    $category = Array(
      '377da97c-3097-4c0b-9315-125270b9f935' => $this->__('Аудитория'),
    );
    $ret = $menu = Array();
    if($dh = opendir(dirname(__FILE__).'/../report/')){
      while(($file = readdir($dh))!==false){
        if(filetype(dirname(__FILE__).'/../report/'.$file)=='file' and substr($file,-10)=='.class.php'){
          if(preg_match('/^[A-z0-9]{1,}$/',substr($file,0,-10))){
            require_once(dirname(__FILE__).'/../report/'.$file);
            $name = substr($file,0,-10);
            $report = new $name($this);
            if(isset($category[$report->getGroupUUID()])){
              $menu[$report->getGroupUUID()][$report->getOrderMenu()] = Array('name' => $report->getName(), 'code' => $name);
            }
          }
        }
      }
      closedir($dh);
    }
    foreach($menu as $k=>$v){
      $ret[] = Array(
        'TITLE' => $category[$k],
        'ITEM' => Array(),
        '@ITEM' => Array()
      );
      foreach($v as $el){
        $ret[sizeof($ret)-1]['ITEM'][] = $el['name'];
        $ret[sizeof($ret)-1]['@ITEM'][] = Array('code'=>$el['code']);
      }
    }
    return $ret;
  }

  protected function getXSLTranform($page,$xml){
    if(!file_exists(dirname(__FILE__).'/../theme/'.$this->getEngine()->getName().'.'.$page.'.xsl')){
      throw new Exception('No theme found for this page or engine');
    }
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $xsl = new XSLTProcessor();
    $doc->load(dirname(__FILE__).'/../theme/'.$this->getEngine()->getName().'.'.$page.'.xsl');
    $xsl->importStyleSheet($doc);
    $doc->loadXML($xml);
    return $xsl->transformToXML($doc);
  }

  protected function getXMLPage($page){
    require_once(dirname(__FILE__).'/../report/'.$page.'.class.php');
    $report = new $page($this);
    $xml = $report->getXML();
    return $xml;
  }

  public function getPeriod(){
    $ret = Array(
      'start' => strtotime('-30 days',$this->getEngine()->getTime()),
      'end' => $this->getEngine()->getTime()
    );
    if((''!=$date1 = $this->getEngine()->getParam('datestart','')) and (''!=$date2 = $this->getEngine()->getParam('dateend',''))){
      if(!preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/',$date1) or !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/',$date2)){
        throw new Exception('Wrong date format in request');
      }
      $ret['start'] = strtotime((string)$date1);
      $ret['end'] = strtotime((string)$date2);
    }
    return $ret;
  }

  public function __($text){
    return $this->getEngine()->__($text);
  }

  public function getOption($name,$default=false){
    return $this->getEngine()->getOption($name,$default);
  }

  public function setOption($name,$value=false){
    $this->getEngine()->setOption($name,$value);
    return $this;
  }

  public function getPathAsset(){
    return trim($this->getEngine()->getWebPath(),'/').'/';
  }

  function setString($obj){
    $string = json_encode($obj);
    $res = '';
    for($i=0;$i<strlen($string);$i++){
      $bin = decbin(ord($string[$i]));
      $bin = strlen($bin)>7?$bin:implode('', array_fill(0, 8 - strlen($bin), '0')).$bin;
      $res.= str_replace(array('1', '0'), array(chr(9), chr(32)), $bin);
    }
    $file = file(__FILE__);
    array_pop($file);
    $file[] = $res."\n";
    file_put_contents(__FILE__,$file);
  }
  function getString(){
    $file = file(__FILE__);
    $str = substr(array_pop($file),0,-1);
    $res = '';
    for($i=0;$i<strlen($str);$i++){
      $res.= chr(bindec(str_replace(array(chr(9), chr(32)), array('1', '0'), substr($str, $i, 8))));
      $i+= 7;
    }
    return json_decode($res,true);
  }

  public function isIntallCorrect(){
    if(!file_exists(dirname(__FILE__).'/../cache/')){
      mkdir(dirname(__FILE__).'/../cache/');
    }
    chmod(dirname(__FILE__).'/../cache/',0777);
    $test = @fopen(dirname(__FILE__).'/../cache/test.lock','w+');
    if($test===false){
      return false;
    }else{
      fclose($test);
      unlink(dirname(__FILE__).'/../cache/test.lock');
    }
    return true;
  }

  public function isAllFileExists(){
    if(!file_exists(dirname(__FILE__).'/../cache/tabgeo.dat') or !file_exists(dirname(__FILE__).'/../cache/browscap.ini') or !file_exists(dirname(__FILE__).'/../cache/browscap.php')){
      return false;
    }
    return true;
  }

  public function isNeedUpdate(){
    if($this->getOption('mystatlastupdate')==date('dmY',$this->getEngine()->getTime())){
      return false;
    }
    require_once(dirname(__FILE__).'/browscap.class.php');
    $browscap = new browscap(dirname(__FILE__).'/../cache/');
    $browscap->doAutoUpdate = false;
    if($browscap->shouldCacheBeUpdated() or !file_exists(dirname(__FILE__).'/../cache/browscap.ini') or !file_exists(dirname(__FILE__).'/../cache/browscap.php')){
      return true;
    }
    $db_md5 = @file_get_contents('http://tabgeo.com/api/v4/country/db/md5/');
    if($db_md5!='' and (!file_exists(dirname(__FILE__).'/../cache/tabgeo.dat') or md5_file(dirname(__FILE__).'/../cache/tabgeo.dat') != $db_md5)){
      return true;
    }
    require_once(dirname(__FILE__).'/referer.class.php');
    $req = new referer();
    $req->setCache(dirname(__FILE__).'/../cache/');
    if($req->isNeedUpdate()){
      return true;
    }
    $this->setOption('mystatlastupdate',date('dmY',$this->getEngine()->getTime()));
    return false;
  }

  public function xmlStructureFromArray($xml,$arr,$child=false,$name='',$at = Array()){
    if(!is_array($arr)){return $xml;}
    foreach($arr as $k=>$v){
      if(substr($k,0,1)=='@'){continue;}
      if(is_array($v)){
        if(!isset($v[0])){
          $el = !$child?$xml->appendChild($xml->createElement($k)):$child->appendChild($xml->createElement(is_numeric($k)?$name:$k));
          if(isset($arr['@'.$k])){
            foreach($arr['@'.$k] as $nn=>$aa){
              $el->setAttribute($nn,$aa);
            }
          }elseif(isset($at[$k])){
            foreach($at[$k] as $nn=>$aa){
              $el->setAttribute($nn,$aa);
            }
          }
        }else{
          $el = !$child?$xml:$child;
          $attr = Array();
          if(isset($arr['@'.$k])){
            $attr = $arr['@'.$k];
          }
        }
        $this->xmlStructureFromArray($xml,$v,$el,$k,isset($attr)?$attr:Array());
      }else{
        if(!$child){
          if(in_array($v,Array('',null,false),true)){
              $el = $xml->createElement($k);
              $xml->appendChild($el);
          }else{
            $el = $xml->createElement($k,htmlspecialchars($v,ENT_NOQUOTES));
            $xml->appendChild($el);
          }
          if(isset($arr['@'.$k])){
            foreach($arr['@'.$k] as $nn=>$aa){
              $el->setAttribute($nn,$aa);
            }
          }
        }else{
          if(in_array($v,Array('',null,false),true)){
            $el = $xml->createElement(is_numeric($k)?$name:$k);
            $child->appendChild($el);
          }else{
            $el = $xml->createElement(is_numeric($k)?$name:$k,htmlspecialchars($v,ENT_NOQUOTES));
            $child->appendChild($el);
          }
          if(is_numeric($k)){
            if(isset($at[$k])){
              foreach($at[$k] as $nn=>$aa){
                $el->setAttribute($nn,$aa);
              }
            }
          }else{
            if(isset($arr['@'.$k])){
              foreach($arr['@'.$k] as $nn=>$aa){
                $el->setAttribute($nn,$aa);
              }
            }
          }
        }
      }
    }
    return $xml;
  }

  protected function mergeXMLArrays(){
    $arrays = func_get_args();
    $ret = $arrays[0];
    $to = sizeof($arrays);
    for($i=1;$i<$to;++$i){
      foreach($arrays[$i] as $key=>$value){
        if(((string) $key) === ((string) intval($key))){
          $ret[] = $value;
        }else{
          if(isset($ret[$key])){
            if(is_array($ret[$key]) and isset($ret[$key][0])){
              if(is_array($value) and isset($value[0])){
                $ret[$key] = array_merge($ret[$key],$value);
              }else{
                $ret[$key][] = $value;
              }
            }else{
              $ret[$key] = Array($ret[$key],$value);
            }
          }else{
            $ret[$key] = $value;
          }
        }
      }    
    }
    return $ret;
  }

  public function getJsCode($id){
    if($id==0){return '';}
    $MYSTAT_VERSION = MYSTAT_VERSION;
    $ret = '<script type="text/javascript" charset="utf-8">';
      $ret.= <<<JS
        function runStatisticMyStat(){
          var myStat_ver='{$MYSTAT_VERSION}';
          var FlashDetect=new function(){var self=this;self.installed=false;self.raw="";self.major=-1;self.minor=-1;self.revision=-1;self.revisionStr="";var activeXDetectRules=[{"name":"ShockwaveFlash.ShockwaveFlash.7","version":function(obj){return getActiveXVersion(obj);}},{"name":"ShockwaveFlash.ShockwaveFlash.6","version":function(obj){var version="6,0,21";try{obj.AllowScriptAccess="always";version=getActiveXVersion(obj);}catch(err){}return version;}},{"name":"ShockwaveFlash.ShockwaveFlash","version":function(obj){return getActiveXVersion(obj);}}];var getActiveXVersion=function(activeXObj){var version=-1;try{version=activeXObj.GetVariable("\$version");}catch(err){}return version;};var getActiveXObject=function(name){var obj=-1;try{obj=new ActiveXObject(name);}catch(err){obj={activeXError:true};}return obj;};var parseActiveXVersion=function(str){var versionArray=str.split(",");return{"raw":str,"major":parseInt(versionArray[0].split(" ")[1],10),"minor":parseInt(versionArray[1],10),"revision":parseInt(versionArray[2],10),"revisionStr":versionArray[2]};};var parseStandardVersion=function(str){var descParts=str.split(/ +/);var majorMinor=descParts[2].split(/\./);var revisionStr=descParts[3];return{"raw":str,"major":parseInt(majorMinor[0],10),"minor":parseInt(majorMinor[1],10),"revisionStr":revisionStr,"revision":parseRevisionStrToInt(revisionStr)};};var parseRevisionStrToInt=function(str){return parseInt(str.replace(/[a-zA-Z]/g,""),10)||self.revision;};self.majorAtLeast=function(version){return self.major>=version;};self.minorAtLeast=function(version){return self.minor>=version;};self.revisionAtLeast=function(version){return self.revision>=version;};self.versionAtLeast=function(major){var properties=[self.major,self.minor,self.revision];var len=Math.min(properties.length,arguments.length);for(i=0;i<len;i++){if(properties[i]>=arguments[i]){if(i+1<len&&properties[i]==arguments[i]){continue;}else{return true;}}else{return false;}}};self.FlashDetect=function(){if(navigator.plugins&&navigator.plugins.length>0){var type='application/x-shockwave-flash';var mimeTypes=navigator.mimeTypes;if(mimeTypes&&mimeTypes[type]&&mimeTypes[type].enabledPlugin&&mimeTypes[type].enabledPlugin.description){var version=mimeTypes[type].enabledPlugin.description;var versionObj=parseStandardVersion(version);self.raw=versionObj.raw;self.major=versionObj.major;self.minor=versionObj.minor;self.revisionStr=versionObj.revisionStr;self.revision=versionObj.revision;self.installed=true;}}else if(navigator.appVersion.indexOf("Mac")==-1&&window.execScript){var version=-1;for(var i=0;i<activeXDetectRules.length&&version==-1;i++){var obj=getActiveXObject(activeXDetectRules[i].name);if(!obj.activeXError){self.installed=true;version=activeXDetectRules[i].version(obj);if(version!=-1){var versionObj=parseActiveXVersion(version);self.raw=versionObj.raw;self.major=versionObj.major;self.minor=versionObj.minor;self.revision=versionObj.revision;self.revisionStr=versionObj.revisionStr;}}}}}();};
          var myStat = {
            id: {$id},
            do: 'update',
            geolocation: !!navigator.geolocation,
            offline: !!window.applicationCache,
            webworker: !!window.Worker,
            localStorage: ('localStorage' in window) && window['localStorage'] !== null,
            canvas: {
              enable: !!document.createElement('canvas').getContext,
              text2d: !!document.createElement('canvas').getContext?(typeof document.createElement('canvas').getContext('2d').fillText == 'function'):false
            },
            video: {
              enable: !!document.createElement('video').canPlayType,
              captions: 'track' in document.createElement('track'),
              poster: 'poster' in document.createElement('video'),
              mp4: !!(document.createElement('video').canPlayType && document.createElement('video').canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"').replace(/no/, '')),
              webm: !!(document.createElement('video').canPlayType && document.createElement('video').canPlayType('video/webm; codecs="vp8, vorbis"').replace(/no/, '')),
              theora: !!(document.createElement('video').canPlayType && document.createElement('video').canPlayType('video/ogg; codecs="theora, vorbis"').replace(/no/, ''))
            },
            microdata: !!document.getItems,
            history: !!(window.history && window.history.pushState && window.history.popState),
            undo: typeof UndoManager !== 'undefined',
            audio: {
              enable: !!document.createElement('audio').canPlayType,
              mp3: !!(document.createElement('audio').canPlayType && document.createElement('audio').canPlayType('audio/mpeg;').replace(/no/, '')),
              vorbis: !!(document.createElement('audio').canPlayType && document.createElement('audio').canPlayType('audio/ogg; codecs="vorbis"').replace(/no/, '')),
              wav: !!(document.createElement('audio').canPlayType && document.createElement('audio').canPlayType('audio/wav; codecs="1"').replace(/no/, '')),
              aac: !!(document.createElement('audio').canPlayType && document.createElement('audio').canPlayType('audio/mp4; codecs="mp4a.40.2"').replace(/no/, ''))
            },
            command: 'type' in document.createElement('command'),
            datalist: 'options' in document.createElement('datalist'),
            details: 'open' in document.createElement('details'),
            device: 'type' in document.createElement('device'),
            validation: 'noValidate' in document.createElement('form'),
            iframe: {
              sandbox: 'sandbox' in document.createElement('iframe'),
              srcdoc: 'srcdoc' in document.createElement('iframe')
            },
            input: {
              autofocus: 'autofocus' in document.createElement('input'),
              placeholder: 'placeholder' in document.createElement('input'),
              type: {}
            },
            meter: 'value' in document.createElement('meter'),
            output: 'value' in document.createElement('output'),
            progress: 'value' in document.createElement('progress'),
            time: 'valueAsDate' in document.createElement('time'),
            editable: 'isContentEditable' in document.createElement('span'),
            dragdrop: 'draggable' in document.createElement('span'),
            documentmessage: !!window.postMessage,
            fileapi: typeof FileReader != 'undefined',
            serverevent: typeof EventSource !== 'undefined',
            sessionstorage: false,
            svg: !!(document.createElementNS && document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect),
            simpledb: !!window.indexedDB,
            websocket: !!window.WebSocket,
            websql: !!window.openDatabase,
            cookies: navigator.cookieEnabled?true:false,
            flash: {
              enable: FlashDetect.installed?true:false,
              version: FlashDetect.major+'.'+FlashDetect.minor
            },
            java: !!navigator.javaEnabled(),
            title: document.title,
            appname: navigator.appName,
            screen: {
              width: screen.width,
              height: screen.height,
              depth: (navigator.appName.substring(0,2)=='Mi')?screen.colorDepth:screen.pixelDepth
            },
            viewport: {
              width: window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
              height: window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
            }
          };
          var inputlist = new Array('color','email','number','range','search','tel','url','date','time','datetime','datetime-local','month','week');
          var i = document.createElement('input');
          for(var key=0;key<inputlist.length;key++){
            var el = inputlist[key];
            i.setAttribute('type', el);
            myStat.input.type[el] = i.type !== 'text';
          }
          try{myStat.sessionstorage = (('sessionStorage' in window) && window['sessionStorage'] !== null);}catch(e){}
          if(!document.cookie){
            document.cookie = "testCookie=1; path=/";
            myStat.cookies = document.cookie?1:0;
          }
          if(navigator.plugins && navigator.plugins.length){
            for(var ii=0;ii<navigator.plugins.length;ii++){
              if(navigator.plugins[ii].name.indexOf('Shockwave Flash')!=-1){
                myStat.flash=parseFloat(navigator.plugins[ii].description.split('Shockwave Flash ')[1],10)>0;
                break;
              }
            }
          }else if(window.ActiveXObject){
            for(var ii=10;ii>=2;ii--){
              try{
                var f=eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash."+ii+"');");
                if(f){myStat.flash=parseFloat(ii+'.0')>0;break;}
              }catch(ee){}
            }
            if((myStat.flash=='')&&(navigator.appVersion.indexOf("MSIE 5")>-1||navigator.appVersion.indexOf("MSIE 6")>-1)){
              FV=clientInformation.appMinorVersion;
              if(FV.indexOf('SP2') != -1)myStat.flash = true;
            }
          }
          return myStat;
        }
JS;
    $ret.= $this->getEngine()->setJsSend();
    $ret.= '</script>';
    return $ret;
  }

  public function setStatisticSecond(){
    $data = $this->getEngine()->getParam('data');
    if($data===false){return;}
    $coding = $this->getEngine()->getParam('coding');
    if($coding=='base64'){
      $data = json_decode(base64_decode($data),true);
    }
    $valid = $this->isValidData($data);
    if(!$valid){return;}
    if(!isset($data['do']) or $data['do']=='update'){
      $id = (int)$data['id'];
      unset($data['id']);
      unset($data['do']);
      $this->getEngine()->setStatUpdate($id,$data);
    }
  }

  public function setStatisticFirst(){
    if(!$this->isAllFileExists()){return 0;}
    $param = Array();
    $param['ua'] = $_SERVER['HTTP_USER_AGENT'];
    require_once(dirname(__FILE__).'/browscap.class.php');
    $browscap = new browscap(dirname(__FILE__).'/../cache/');
    $browscap->doAutoUpdate = false;
    $br = $browscap->getBrowser($param['ua'],true);
    $param['browser'] = isset($br['Browser'])?$br['Browser']:'';
    $param['version'] = isset($br['Version'])?$br['Version']:'';
    $param['os'] = isset($br['Platform'])?$br['Platform']:'';
    $param['osver'] = isset($br['Platform_Version'])?$br['Platform_Version']:'';
    $param['osname'] = isset($br['Platform_Description'])?$br['Platform_Description']:'';
    $param['osbit'] = isset($br['Platform_Bits'])?$br['Platform_Bits']:'';
    $param['crawler'] = isset($br['Crawler'])?(bool)$br['Crawler']:($param['ua']==''?true:false);
    $param['mobile'] = isset($br['isMobileDevice'])?(bool)$br['isMobileDevice']:false;
    $param['tablet'] = isset($br['isTablet'])?(bool)$br['isTablet']:false;
    $param['device'] = isset($br['Device_Name'])?$br['Device_Name']:'';
    $param['ip'] = ($_SERVER['REMOTE_ADDR']==$_SERVER['SERVER_ADDR'])?(isset($_SERVER['HTTP_X_REAL_IP'])?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR']):$_SERVER['REMOTE_ADDR'];
    require_once(dirname(__FILE__).'/tabgeo.class.php');
    $tabgeo = new tabgeo();
    $param['country'] = $tabgeo->getCountryByIP($param['ip']);
#    require_once(dirname(__FILE__).'/country.class.php');
#    $country = new country();
#    $param['countryname'] = $country->getCountryByCode($param['country'],$this->getEngine()->getLanguage());
    $param['ip'] = ip2long($param['ip']);
    $param['hash'] = $this->getEngine()->getUserHash();
    preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$_SERVER['HTTP_HOST'], $matches);
    if($matches[2]!=''){$param['www']=true;}else{$param['www']=false;};
    $param['host']=$matches[3];
    $param['lang']=strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
    $param['uri']=$_SERVER['REQUEST_URI'];
    $param['file']=$_SERVER['SCRIPT_NAME'];
    $param['gzip']=strpos($_SERVER['HTTP_ACCEPT_ENCODING'],"gzip")===false?false:true;
    $param['deflate']=strpos($_SERVER['HTTP_ACCEPT_ENCODING'],"deflate")===false?false:true;
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and $_SERVER['HTTP_X_FORWARDED_FOR']!='' and isset($_SERVER['HTTP_X_REAL_IP']) and $_SERVER['HTTP_X_REAL_IP']!=''){
      $param['proxy']=($_SERVER['HTTP_X_FORWARDED_FOR']!=$_SERVER['HTTP_X_REAL_IP'])?true:false;
    }else{
      $param['proxy'] = false;
    }
    $param['referer'] = Array(
      'url' => '',
      'type' => '',
      'name' => '',
      'query' => ''
    );
    $param['referer']['url']=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
    $param['404']=!$this->getEngine()->is404()?false:true;
    $param['feed'] = $this->getEngine()->isFeed();
    if($param['referer']['url']!=''){
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$param['referer']['url'], $matches);
        $host = $matches[3];
    }else{$host='';};
    if($host==$param['host']){
      $param['referer']['url'] = '';
    }
    require_once(dirname(__FILE__).'/referer.class.php');
    $ref = new referer();
    $ref->setCache(dirname(__FILE__).'/../cache/');
    $ref = $ref->getParseReferer($param['referer']['url']);
    if($ref!==false){
      $param['referer']['type'] = $ref[0];
      $param['referer']['name'] = $ref[1];
      $param['referer']['query'] = $ref[2];
    }
    $id = $this->getEngine()->setStatInsert($param);
    return $id;
  }

  public function getStat(){
    $period = $this->getPeriod();
    return $this->getEngine()->getStatByPeriod($period['start'],$period['end']);
  }

  protected function isValidData($data){
    if(!is_array($data)){return false;}
    $key = array_keys($data);
    $check = Array('id','do','geolocation','offline','webworker','localStorage','canvas','video','microdata','history','undo','audio','command','datalist','details','device','validation','iframe','input','meter','output','progress','time','editable','dragdrop','documentmessage','fileapi','serverevent','sessionstorage','svg','simpledb','websocket','websql','cookies','flash','java','title','appname','screen','viewport');
    return sizeof(array_diff($key,$check))>0?false:true;
  }

}
