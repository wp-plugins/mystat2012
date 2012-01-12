<?php

class IP_Addresses{

    static function getTitle(){
        return __("IP Addresses","myStat");
    }

    static function getMenuItemName(){
        return array(__('IP Addresses','myStat'),1);
    }

    static function getMenuTreeName(){
        return array(__('Geography','myStat'),4);
    }

    function init($date,$page=1,$exp='',$num=0){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        if($exp!=''){
            echo $num.'###';
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            echo "<tr><td>".$cmn->whois(long2ip($exp))."</td></tr>";
            echo "</table>";
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING SUB (report '.$this->getTitle().')');};
            return "NODATE";
            exit();
        };
        $limit=30;$page--;
        $all_page=$cmn->getSQLONE("SELECT count(DISTINCT ip) as sm FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200;");
        $var1=$cmn->getSQL("SELECT ip,sum(count) as sm FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 GROUP BY ip ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";");
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_IP_Addresses(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<script>";
        echo "function myStat_expand(num,page){";
        echo "page=page++;";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_IP_Addresses(page,id[num],num,'myStat_expand_load');";
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
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("IP-address info","myStat")."' title='".__("IP-address info","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.",\"".$page."\");}else{el.style.display=\"none\";};'/> ".long2ip($var1[$i][0])."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_IP_Addresses(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
    }
};
?>