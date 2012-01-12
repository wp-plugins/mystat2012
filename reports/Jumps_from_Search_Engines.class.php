<?php

class Jumps_from_Search_Engines{

    static function getTitle(){
        return __("Jumps from Search Engines","myStat");
    }

    static function getMenuItemName(){
        return array(__('Jumps from Search Engines','myStat'),2);
    }

    static function getMenuTreeName(){
        return array(__('Referrers','myStat'),3);
    }

    function init($date,$exp='',$num=0){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        if($exp!=''){
            echo $num.'###';
            preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
            $host=$matches[3];
            $var1=$cmn->getSQL("SELECT value3 FROM %%PREFIX%%myStat_data WHERE type='2' AND value1='s' AND value2='".$exp."';");
            $sql='';
            for($i=0;$i<count($var1);$i++){
                $sql.=($sql!=''?" OR":"(")." LOCATE('".$var1[$i][0]."',referer)!=0";
            };
            $sql.=")";
            $var=$cmn->getSQL("SELECT referer,count(referer) as sm FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND $sql AND code_stat=200 GROUP BY referer ORDER BY sm DESC LIMIT 0,20;");
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='".$var[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var[$i][0], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][1],0,',',' ')."</td></tr>";
            };
            if(count($var)==0){echo "<tr><td>".__("No URL","myStat")."</td></tr>";};
            echo "</table>";
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING SUB (report '.$this->getTitle().')');};
            return "NODATE";
            exit();
        };
        $var1=$cmn->getSQL("SELECT t1.value2,count(t2.referer) as sm,referer FROM %%PREFIX%%myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t2.referer!='' AND t2.code_stat=200 AND t1.type='2' AND value1='s' AND LOCATE(value3,referer)!=0 GROUP BY t1.value2 ORDER BY sm DESC;");
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_Jumps_from_Search_Engines(id[num],num,'myStat_expand_load');";
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
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 most popular addresses","myStat")."' title='".__("Show 20 most popular addresses","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> ".$var1[$i][0]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Jumps from Search Engines","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        $all_page=$cmn->getSQLONE("SELECT count(DISTINCT referer) FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer!='' AND code_stat=200;");
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other links","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page-$sum,0,',',' ')."</td></tr>";
        $all_page=$cmn->getSQLONE("SELECT count(referer) FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer='' AND code_stat=200;");
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Direct jump (without a referrer)","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }
};
?>