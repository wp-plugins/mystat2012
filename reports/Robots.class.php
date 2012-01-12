<?php

class Robots{

    static function getTitle(){
        return __("Robots","myStat");
    }

    static function getMenuItemName(){
        return array(__('Robots','myStat'),8);
    }

    static function getMenuTreeName(){
        return array(__('System','myStat'),5);
    }

    function __construct(){
    }

    function init($date,$page=1,$max=0,$exp='',$num=0){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        if($exp!=''){
            echo $num.'###';
            $rob=$cmn->getSQL("SELECT value2 FROM %%PREFIX%%myStat_data WHERE  value1='".$exp."' AND type='3';");
            $var1=$cmn->getSQL("SELECT user_agent,ip FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title='' OR date_load='0000-00-00 00:00:00') GROUP BY user_agent;");
            $wh='AND (';
            for($i=0;$i<count($var1);$i++){
                for($j=0;$j<count($rob);$j++){
                    $key='^'.str_replace(array('\\','.','?','*','^','$','[',']','|','(',')','+','{','}','%','/'),array('\\\\','\\.','.','.*','\\^','\\$','\\[','\\]','\\|','\\(','\\)','\\+','\\{','\\}','\\%','\\/'),$rob[$j][0]).'$';
                    if(preg_match('%'.$key.'%i',$var1[$i][0])){
                        $wh.=($wh!='AND ('?" OR ":"")."(user_agent='".$var1[$i][0]."')";
                        break;
                    };
                };
            };
            $wh.=")";
            $var1=$cmn->getSQL("SELECT min(date),max(date) FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."')".$wh.";");
            echo "<table cellspacing='0' cellpadding='0' style='background-color:#F2F2F2;border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("The first visit","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".$var1[0][0]."</td></tr>";
            echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("The last visit","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".$var1[0][1]."</td></tr>";
            echo "</table>";
            $var1=$cmn->getSQL("SELECT ip,count(*) as sm FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."')".$wh." GROUP BY ip ORDER BY sm DESC LIMIT 0,40;");
            echo "<table cellspacing='0' cellpadding='0' style='background-color:#F2F2F2;border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            $j=0;
            for($i=0;$i<count($var1);$i=$i+4){
               echo "<tr".(floor($j/2)==$j/2?" style='background-color:#E6E6E6'":"").">";
               echo "<td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'".($var1[$i][0]!=''?">".long2ip($var1[$i][0])."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".$var1[$i][1]:" colspan='2'>&nbsp;")."</td>";
               echo "<td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'".($var1[$i+1][0]!=''?">".long2ip($var1[$i+1][0])."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".$var1[$i+1][1]:" colspan='2'>&nbsp;")."</td>";
               echo "<td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'".($var1[$i+2][0]!=''?">".long2ip($var1[$i+2][0])."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".$var1[$i+2][1]:" colspan='2'>&nbsp;")."</td>";
               echo "<td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'".($var1[$i+3][0]!=''?">".long2ip($var1[$i+3][0])."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".$var1[$i+3][1]:" colspan='2'>&nbsp;")."</td>";
               echo "</tr>";    
               $j++;
            };
            echo "</table>";
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING SUB (report '.$this->getTitle().')');};
            return "NODATE";
            exit();
        };
        $limit=30;$page--;
        $all_page=$cmn->getSQLONE("SELECT count(DISTINCT user_agent) FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title='' OR date_load='0000-00-00 00:00:00');");
        $type=$cmn->getSQL("SELECT value1,value2 FROM %%PREFIX%%myStat_data WHERE type='3' ORDER BY value3;");
        $limit=$all_page;
        $var1=$cmn->getSQL("SELECT user_agent,count(user_agent) as sm FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title='' OR date_load='0000-00-00 00:00:00') GROUP BY user_agent ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";");
        $tt=array();
        foreach($type as $tval){
            $key='^'.str_replace(array('\\','.','?','*','^','$','[',']','|','(',')','+','{','}','%'),array('\\\\','\\.','.','.*','\\^','\\$','\\[','\\]','\\|','\\(','\\)','\\+','\\{','\\}','\\%'),$tval[1]).'$';
            for($i=0;$i<count($var1);$i++){
                if(preg_match('%'.$key.'%i',$var1[$i][0])){
                    $tt[][0]=$tval[0];
                    $tt[count($tt)-1][1]=$var1[$i][1];
                    $var1[$i][0]=null;$var1[$i][1]=null;
                };
            };
        };
        $no_detect=0;
        for($i=0;$i<count($var1);$i++){
            if($var1[$i][0]!=null){
                $no_detect++;
            };
        };
        $new=array();
        for($i=0;$i<count($tt);$i++){
            if(!array_key_exists($tt[$i][0],$new)){
                if($tt[$i][0]!=''){
                    $new[$tt[$i][0]]=$tt[$i][1];
                };
            }else{
                $new[$tt[$i][0]]+=$tt[$i][1];
            };
        };
        $sum=0;reset($new);
        while($fn = current($new)){
            $sum+=$fn;
            if($max<$fn){$max=$fn;};
            next($new);
        };
        if($sum<1 and count($new)>0){$sum=1;};
        arsort($new);
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        $i=0;reset($new);
        while($fn = current($new)){
            echo "id[".$i."]='".mysql_escape_string(key($new))."';";
            $i++;
            next($new);
        };
        echo "x_Robots('".($page+1)."','".$max."',id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Robots(\'[page_number]\','.$max.',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $i=0;reset($new);
        while($fn = current($new)){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("About this robot","myStat")."' title='".__("About this robot","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> \n";
            echo key($new);
            echo "<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$fn/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($fn,0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$fn/$sum,2,',',' ')."%</sup></td></tr>";
            $i++;
            next($new);
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Robots(\'[page_number]\','.$max.',\'myStat_load\');',$all_page,$tmp,$limit);
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Robots","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other Robots","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($no_detect,0,',',' ')."</td></tr>";
        echo "</table>";
    }
};
?>