<?php
class KA{
 var $LOADER = '';
 var $debug_mode = 0;
 var $export_list = array();
 var $request_type = "POST";
 var $ka_js_has_been_shown = 0;
 var $js_error='';
 var $timeout=60;
 var $header="Content-type: text/html; charset=UTF-8\n\n";
 var $CACHE=0;
 var $load_func_before="";
 var $load_func_after="";
 var $load_func_before_arg=null;
 var $load_func_after_arg=null;

 function KA(){
 }

 function VERSION(){
  return '1.5';
 }

 function ka_get_my_uri(){
  $tt="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  return $tt;
 }

 function ka_php2js($a){
  if(is_null($a)) return 'null';
  if($a === false) return 'false';
  if($a === true) return 'true';
  if(is_scalar($a)){
   $a = addslashes($a);
//   $a = str_replace("'", '\\\'', $a);
   $a = str_replace("\n", '\n', $a);
   $a = str_replace("\r", '\r', $a);
   return "'$a'";
  };
  $isList = true;
  for ($i=0, reset($a); $i<count($a); $i++, next($a))
   if (key($a) !== $i) { $isList = false; break; }
  $result = array();
  if ($isList) {
   foreach ($a as $v) $result[] = KA::_php2js($v);
   return '[ ' . join(',', $result) . ' ]';
  }else{
   foreach ($a as $k=>$v) $result[] = KA::_php2js($k) . ': ' . KA::_php2js($v);
   return '{ ' . join(',', $result) . ' }';
  };
 }

 function ka_get_one_stub($func_name){
  $html="function x_$func_name(){ka_do_call(\"$func_name\",x_$func_name.arguments);};";
  return $html;
 }

 function ka_show_one_stub($func_name){
  echo $this->ka_get_one_stub($func_name);
 }

 function export($aa){
   $this->export_list = $aa;
 }

 function get_javascript(){
  $html = "";
  if(! $this->ka_js_has_been_shown){
   $html .= $this->ka_get_common_js();
   $this->ka_js_has_been_shown = 1;
  };
  foreach ($this->export_list as $func) {
   $html .= $this->ka_get_one_stub($func);
  };
  return $html;
 }

 function show_javascript(){
  echo $this->get_javascript();
 }

 function init() {
  $mode = "";
  if (!empty($_GET["rs"])) 
   $mode = "get";
  if (!empty($_POST["rs"]))
   $mode = "post";
  if (empty($mode)) return;
  if ($mode == "get"){
   $this->request_type="GET";
   // Bust cache in the head
   header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
   header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
   // always modified
   header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
   header ("Pragma: no-cache");                          // HTTP/1.0
   if(!empty($_GET["rback"])){
    $rback=$_GET["rback"];
   };
   $func_name = $_GET["rs"];
   if (! empty($_GET["rsargs"])) 
    $args = $_GET["rsargs"];
   else
    $args = array();
  }else {
   $this->request_type="POST";
   $func_name = $_POST["rs"];
   if (! empty($_POST["rsargs"])) 
    $args = $_POST["rsargs"];
   else
    $args = array();
  };
  if(is_array($args)){
   for($i=0;$i<count($args);$i++){
    $args[$i]=stripslashes($args[$i]);
   };
  }else{$args=stripslashes($args);};
  Header($this->header);
  if (! in_array($func_name, $this->export_list)){
   if(!empty($rback)){
    $this->LOADER='SCRIPT';
    echo "clearTimeout(ka_timeout);".$rback."('\\'$func_name\\' not callable');";
   }else{
    $this->LOADER='XML';
    echo "'$func_name' not callable";
   };
  }else{
   if(empty($rback)){$this->LOADER='XML';}else{$this->LOADER='SCRIPT';};
   $result='';
   ob_start();
   if(!empty($this->load_func_before))$result.= call_user_func_array($this->load_func_before,$this->load_func_before_arg);
   $strClass=new Report();
   $result.= $strClass->getLoadModule($func_name,$args);
   if(!empty($this->load_func_after))$result.= call_user_func_array($this->load_func_after,$this->load_func_after_arg);
   $text = ob_get_contents();
   ob_end_clean();
   if($result==''){$result=$text;};
   if(!empty($rback)){
    $result="clearTimeout(ka_timeout);".$rback."(".$this->ka_php2js($result).");";
   };
   echo $result;
  };
  exit;
 }

 function JScopy2id($data,$id){
  echo "ka_rexp1 = /(\\<scri\\pt[^>]*?>[^<]*(?:(?!<\\/scri\\pt>)<[^<]*)*<\\/scri\\pt>)/gi;";
  echo "ka_rexp2=/(\\<scri\\pt[^>]*?>|<\\/scri\\pt>)/gi;";
  echo "ka_split=".$data.".split(\"<\\/s\"+\"cript>\");";
  echo "document.getElementById('".$id."').innerHTML='';";
  echo "for(ka_j=0;ka_j<ka_split.length;ka_j++){";
  echo "if(ka_split[ka_j].search(/\\<scri\\pt[^>]*?>/i)!=-1){";
  echo "ka_split[ka_j]+='<\\/s'+'cript>';";
  echo "ka_found=ka_split[ka_j].match(ka_rexp1);";
  echo "document.getElementById('".$id."').innerHTML+=ka_split[ka_j].replace(/\\<scri\\pt[^>]*?>.*?<\\/scri\\pt>/gi,' ');";
  echo "for(ka_i=0;ka_i<ka_found.length;ka_i++){";
  echo "ka_fou=ka_found[ka_i].replace(ka_rexp2,'');";
  echo "ka_script=document.createElement('script');";
  echo "document.getElementById('".$id."').appendChild(ka_script).text=ka_fou;";
//  echo "if(ka_script.text!=ka_fou){eval(ka_fou);};";
  echo "};";
  echo "}else{document.getElementById('".$id."').innerHTML+=ka_split[ka_j];};";
  echo "};";
 }

 function ka_get_common_js() {
  $ka_remote_uri= $this->ka_get_my_uri();
  $t = strtoupper($this->request_type);
  if($t != "GET" && $t != "POST"){$t="POST";};
  ob_start();
  ?>
var ka_debug_mode = <?php echo $this->debug_mode ? "true" : "false"; ?>;
var ka_request_type = "<?php echo $t; ?>";
var ka_timeout='';
var ka_cache=<?php echo $this->CACHE;?>;
var ka_cache_list;
function ka_debug(text){
 if (ka_debug_mode)alert("MSG: " + text);
};
function ka_init_object() {
 ka_debug("ka_init_object() called..");
 var A=null;
 try{
  A=new ActiveXObject("Msxml2.XMLHTTP");
 }catch(e){
  try{
   A=new ActiveXObject("Microsoft.XMLHTTP");
  }catch(oc){
   try{
    if(!A && typeof XMLHttpRequest != "undefined") A = new XMLHttpRequest();
   }catch(aj){
    A=null;
   };
  };
 };
 if(!A)ka_debug("Could not create connection object.");
 return A;
};

function ka_error_status(type){
 if(type=='SCRIPT'){
  clearTimeout(ka_timeout);
  span=document.getElementById("ko_script");
  span.innerHTML='';
 }else{x=null;};
<?php echo $this->js_error;?> 
};

function save_cache(url,post_data,data){
 if(ka_cache!=0){
  if(post_data!=null){url+=post_data;};
  if(ka_cache_list.length<ka_cache){
  }else{
   
  };
  alert(ka_cache_list[0]);
 };
};

function load_cache(url,func){
 return false;
};

function ka_do_call(func_name, args){
 var i, x, n;
 var uri;
 var post_data;
 uri = "<?php echo $ka_remote_uri; ?>";
 if(ka_request_type == "GET"){
  if(uri.indexOf("?") == -1)uri=uri + '?rs=' + escape(func_name);
  else uri=uri + "&rs=" + escape(func_name);
  for(i = 0; i < args.length-1; i++) uri = uri + '&rsargs[]=' + escape(args[i]).replace(/\+/g,'%2B');
  uri = uri + '&rsrnd=' + new Date().getTime();
  post_data = null;
 }else{
  post_data = 'rs=' + escape(func_name);
  for(i = 0; i < args.length-1; i++)post_data = post_data + '&rsargs[]=' + escape(args[i]).replace(/\+/g,'%2B');
 };
 if(load_cache(uri,args[args.length-1])){return;};
 x = ka_init_object();
 if(x){
  x.open(ka_request_type, unescape(uri), true);
  if(ka_request_type == "POST"){
   x.setRequestHeader("Method", "POST " + uri + " HTTP/1.1");
   x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  };
  x.onreadystatechange = function(){
   clearTimeout(ka_timeout);
   if((x.readyState != 4)||(x.status != 200))return;
   ka_debug("received " + x.responseText);
   var data = x.responseText;
   data=data.replace(/\\/g,"\\\\").replace(/'/g,"\\'").replace(/\n/g,"\\n").replace(/\r/g,"\\r").replace(/<script/g,"<script defer=true");
   save_cache(uri,post_data,data);
   eval(args[args.length-1]+"('"+data+"')");
   x=null;
  };
  x.send(post_data);
  if(!ka_timeout)ka_timeout=setTimeout(function(){x=null;ka_error_status('XML');},<?php echo $this->timeout;?>000);
 }else{
  uri = "<?php echo $ka_remote_uri; ?>";
  if(uri.indexOf("?") == -1)uri=uri + '?rs=' + escape(func_name)+"&rback="+escape(args[args.length-1]);
  else uri=uri + "&rs=" + escape(func_name)+"&rback="+escape(args[args.length-1]);
  for(i = 0; i < args.length-1; i++) uri = uri + '&rsargs[]=' + escape(args[i]).replace(/\+/g,'%2B');
  uri = uri + '&rsrnd=' + new Date().getTime();
  post_data = null;
  ka_request_type="GET";
  if(!ka_timeout)ka_timeout=setTimeout(function(){ka_error_status('SCRIPT');},<?php echo $this->timeout;?>000);
  var span = null;
  span=document.getElementById("ko_script");
  if(!span){
   span=document.createElement("SPAN");
   span.style.display = 'none';
   span.id='ko_script';
   document.getElementsByTagName("body")[0].appendChild(span);
  };
  span.innerHTML='<s'+'cript defer=true></s'+'cript>';
  var s = span.getElementsByTagName("script")[0];
  if(!s){span.innerHTML='';span.appendChild(document.createElement('script'));s = span.getElementsByTagName("script")[0];};
  ka_debug("Create Script element..."+s);
  s.language = "JavaScript";
  s.defer=true;
  ka_debug("Load script data...");
  if (s.setAttribute) s.setAttribute('src', uri); else s.src = uri;
 };
 ka_debug(func_name + " uri = " + uri + "\n post = " + post_data);
 ka_debug(func_name + " waiting..");
 
};
<?php
  $html = ob_get_contents();
  $html=str_replace("\r","",$html);
  $html=str_replace("\n","",$html);
  ob_end_clean();
  return $html;
 }


};
?>