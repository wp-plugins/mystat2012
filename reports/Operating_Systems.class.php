<?php

class Operating_Systems{

    static function getTitle(){
        return __("Operating Systems","myStat");
    }

    static function getMenuItemName(){
        return array(__('Operating Systems','myStat'),7);
    }

    static function getMenuTreeName(){
        return array(__('System','myStat'),5);
    }

    function init($date,$exp='',$num=0){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        if($exp!=''){
            echo $num.'###';
            $x16=$cmn->getSQLONE("SELECT count(t2.user_agent) as sm FROM %%PREFIX%%myStat_data t1, %%PREFIX%%myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t1.value1='".$exp."' AND t2.user_agent!='' AND t1.type='4' AND LOCATE(value2,user_agent)!=0 AND LOCATE('Win3.1',user_agent)!=0;");
            $x64=$cmn->getSQLONE("SELECT count(t2.user_agent) as sm FROM %%PREFIX%%myStat_data t1, %%PREFIX%%myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t1.value1='".$exp."' AND t2.user_agent!='' AND t1.type='4' AND LOCATE(value2,user_agent)!=0 AND (LOCATE('WOW64',user_agent)!=0 OR LOCATE('Win64',user_agent)!=0 OR LOCATE('x64',user_agent)!=0);");
            $x32=$cmn->getSQLONE("SELECT count(t2.user_agent) as sm FROM %%PREFIX%%myStat_data t1, %%PREFIX%%myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t1.value1='".$exp."' AND t2.user_agent!='' AND t1.type='4' AND LOCATE(value2,user_agent)!=0;");
            $x32=$x32-$x16-$x64;
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            if($x16>0){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("16 bit systems","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($x16,0,',',' ')."</td></tr>";
            };
            if($x32>0){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("32 bit systems","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($x32,0,',',' ')."</td></tr>";
            };
            if($x64>0){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("64 bit systems","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($x64,0,',',' ')."</td></tr>";
            };
            echo "</table>";
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING SUB (report '.$this->getTitle().')');};
            return "NODATE";
            exit();
        };
        $var1=$cmn->getSQL("SELECT t1.value1,count(t2.user_agent) as sm,user_agent FROM %%PREFIX%%myStat_data t1, %%PREFIX%%myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t2.user_agent!='' AND t1.type='4' AND LOCATE(value2,user_agent)!=0 GROUP BY t1.value1 ORDER BY sm DESC;");
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_Operating_Systems(id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Type of systems","myStat")."' title='".__("Type of systems","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> ".$var1[$i][0]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Operating Systems","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "</table>";
    }
};
?>