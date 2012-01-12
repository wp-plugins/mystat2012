<?php

class Report{

    private $dir;

    function __construct(){
        $this->dir=WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/../reports/';
    }

    function head_page($name='',$page=''){
        global $cmn;
        $test_var='';
        if(isset($_GET['date1']) and isset($_GET['date2'])){
            $var[0][0]=substr($_GET['date1'],0,10);
            $var[0][1]=substr($_GET['date2'],0,10);
        }else{
            $var=$cmn->getSQL("SELECT DATE_FORMAT(now() - INTERVAL 1 MONTH,'%Y-%m-%d'),DATE_FORMAT(max(date),'%Y-%m-%d'),DATE_FORMAT(now() - INTERVAL 1 DAY,'%Y-%m-%d'),DATE_FORMAT(now() - INTERVAL 1 WEEK,'%Y-%m-%d'),DATE_FORMAT(min(date),'%Y-%m-%d'),DATE_FORMAT(now() - INTERVAL WEEKDAY(now()) DAY,'%Y-%m-%d'),DATE_FORMAT(now(),'%Y-%m-%d') FROM %%PREFIX%%myStat_main;");
            if($var[0][1]==null){$var[0][1]=$var[0][6];};
            $test_var=$var;
        };
        if($name!=''){
            if(!is_array($test_var)){
                $test_var=$cmn->getSQL("SELECT DATE_FORMAT(max(date) - INTERVAL 1 MONTH,'%Y-%m-%d'),DATE_FORMAT(max(date),'%Y-%m-%d'),DATE_FORMAT(max(date) - INTERVAL 1 DAY,'%Y-%m-%d'),DATE_FORMAT(max(date) - INTERVAL 1 WEEK,'%Y-%m-%d'),DATE_FORMAT(min(date),'%Y-%m-%d'),DATE_FORMAT(max(date) - INTERVAL WEEKDAY(max(date)) DAY,'%Y-%m-%d') FROM %%PREFIX%%myStat_main;");
            };
            echo "<form name='myStat_date_form' method='get'><input type='hidden' name='page' value='".dirname(dirname(plugin_basename(__FILE__)))."/mystat.php' />";
            echo "<table width='100%' border='0' style='padding:0;margin:0'>";
            echo "<tr><td nowrap style='padding-right:5px'><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/report_description.gif' width='12px' height='14px' /></td><td nowrap width='100%'><b>".$name."</b></td></tr>";
            echo "<tr><td>&nbsp;</td><td><sup>".sprintf(__("The report for the period from %s to %s","myStat"),"<input name='date1'onclick=\"A_TCALS['myStat_d1'].f_toggle();\" id='tcalico_myStat_d1' type='text' readonly style='text-align:center;font-size:10px;font-weight:bold;cursor:pointer;border:1px silver solid;color:black;background-color:#DDDDDD;margin:3px;padding:3px;-moz-border-radius:4px;' value='".$var[0][0]." 00:00:00' />","<input name='date2' onclick=\"A_TCALS['myStat_d2'].f_toggle();\" id='tcalico_myStat_d2' type='text' readonly style='font-size:10px;font-weight:bold;cursor:pointer;border:1px silver solid;color:black;background-color:#DDDDDD;margin:3px;padding:3px;-moz-border-radius:4px;text-align:center;' value='".$var[0][1]." 23:59:59' />")."<b onclick=\"myStat_date_form.submit();\" style='cursor:pointer;border:1px silver solid;color:black;background-color:#DDDDDD;margin:3px;padding:3px;-moz-border-radius:4px;'>".__("OK","myStat")."</b></sup></td></tr>";
            echo "<tr><td>&nbsp;</td><td nowrap align='center'><sup>[ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][4]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][4] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("All","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][1]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][1] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("Today","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][2]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][2]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][2] and $var[0][1]==$test_var[0][2])?"color:red;text-decoration:underline;":"")."'>".__("Yesterday","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][5]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][5] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("This Week","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".substr($test_var[0][1],0,8)."01 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==substr($test_var[0][1],0,8).'01' and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("This Month","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][3]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][3] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("Last 7 Days","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][0]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][0] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("Last 30 Days","myStat")."</b> ]</sup></td></tr>";
            echo "</table>";
            echo "<input type='hidden' name='myStat_page' value='".$page."' /></form>";
            echo "<script>";
            echo "new tcal ({'formname':'myStat_date_form','controlname':'date1','id':'myStat_d1'});new tcal ({'formname':'myStat_date_form','controlname':'date2','id':'myStat_d2'});";
            echo "</script>";

        };
        return array($var[0][0]." 00:00:00",$var[0][1]." 23:59:59");
    }

    function getModule(){
        $dir=$this->dir;
        if(is_dir($dir)){
            $rep=scandir($dir);
            $repFile=array();
            for($i=2;$i<count($rep);$i++){
                if(is_file($dir.$rep[$i])){
                    $tmp=explode('.',$rep[$i]);
                    include_once($dir.$rep[$i]);
                    if(class_exists($tmp[0])){
                        $repFile[]=$tmp[0];
                    };
                };
            };
            return $repFile;
        };
        return false;
    }

    function getTree(){
        $dir=$this->dir;
        $mod=$this->getModule();
        $allTree=array();
        for($i=0;$i<count($mod);$i++){
            if(!class_exists($mod[$i])){
                include_once($dir.$mod[$i].".class.php");
            };
            $tt=$this->getMenuTreeName($mod[$i]);
            if(!in_array($tt[0],$allTree)){
                if($tt[1]!=null){
                    $allTree[$tt[1]]=$tt[0];
                }else{
                    $allTree[count($mod)+$i]=$tt[0];
                };
            };
        };
        ksort($allTree);
        $allTree1=$allTree;$allTree=array();
        foreach($allTree1 as $l){
            if(isset($l)){
                $allTree[]=$l;
            };
        };
        return $allTree;
    }

    function getItem($tree){
        $dir=$this->dir;
        $mod=$this->getModule();
        $allItem=array();
        for($i=0;$i<count($mod);$i++){
            if(!class_exists($mod[$i])){
                include_once($dir.$mod[$i].".class.php");
            };
            $tt=$this->getMenuItemName($mod[$i]);
            $tt1=$this->getMenuTreeName($mod[$i]);
            if($tt1[0]==$tree){
                if($tt[1]!=null and !isset($allItem[$tt[1]])){
                    $allItem[$tt[1]]=$tt[0];
                }else{
                    $allItem[count($mod)+$i]=$tt[0];
                };
            };
        };
        ksort($allItem);
        $allItem1=$allItem;$allItem=array();
        foreach($allItem1 as $l){
            if(isset($l)){
                $allItem[]=$l;
            };
        };
        return $allItem;
    }

    function getLoadModule($name="",$argumens=array()){
        $dir=$this->dir;
        if($name==''){
            $name=$this->getClass($this->getDefaultItem());
        };
        if(!class_exists($name)){
            include_once($dir.$name.".class.php");
        };
        eval("\$mod=new ".$name."();");
        ob_start();
        $date=$this->head_page($this->getTitle($name),$name);
        $text = ob_get_contents();
        ob_end_clean();
        $aa[]=$date;
        for($i=0;$i<count($argumens);$i++){
            $aa[]=$argumens[$i];
        };
        ob_start();
        $r=call_user_func_array(Array($mod,"init"),$aa);
        $text1 = ob_get_contents();
        ob_end_clean();
        if($r!='NODATE'){echo $text;};
        echo $text1;
    }

    function getClass($item,$tree=""){
        $dir=$this->dir;
        $mod=$this->getModule();
        $class='';
        for($i=0;$i<count($mod);$i++){
            if(!class_exists($mod[$i])){
                include_once($dir.$mod[$i].".class.php");
            };
            $tt=$this->getMenuItemName($mod[$i]);
            if($tree!=""){
                $tt1=$this->getMenuTreeName($mod[$i]);
                if($tt[0]==$item and $tt1[0]==$tree){
                    return $mod[$i];
                };
            }else{
                if($tt[0]==$item){
                    return $mod[$i];
                };
            };
        };
        return '';
    }

    function getMenuTreeName($name){
        $tt=$name."::getMenuTreeName();";
        eval("\$tt=".$tt);
        if(!is_array($tt)){
            $tt[0]=$tt;
            $tt[1]=null;
        };
        return $tt;
    }

    function getMenuItemName($name){
        $tt=$name."::getMenuItemName();";
        eval("\$tt=".$tt);
        if(!is_array($tt)){
            $tt[0]=$tt;
            $tt[1]=null;
        };
        return $tt;
    }

    function getDefaultItem(){
        $array=$this->getTree();
        $array=$this->getItem($array[0]);
        return $array[0];
    }

    function getTitle($name){
        $tt=$name."::getTitle();";
        eval("\$tt=".$tt);
        return $tt;
    }

}

?>