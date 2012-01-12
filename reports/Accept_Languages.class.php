<?php

class Accept_Languages{

    static function getTitle(){
        return __("Accept-Languages","myStat");
    }

    static function getMenuItemName(){
        return array(__('Accept-Languages','myStat'),2);
    }

    static function getMenuTreeName(){
        return array(__('System','myStat'),5);
    }

    function init($date,$page=1){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        $limit=30;$page--;
        $var2=$cmn->getSQL("SELECT count(DISTINCT ip) FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY lang;");
        $all_page=count($var2);
        $var1=$cmn->getSQL("SELECT lang,count(DISTINCT ip) as sm,country FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY lang ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";");
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Accept_Languages(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var2);$i++){
            $sum+=$var2[$i][0];
            if($max<$var2[$i][0]){$max=$var2[$i][0];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        include_once(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/../modules/geoip/locale.php');
        $loc=new Locale();
        for($i=0;$i<count($var1);$i++){
            $tmp=$loc->get_locale(strtolower($var1[$i][0]));
            if(file_exists(WP_PLUGIN_DIR."/".dirname(dirname(plugin_basename(__FILE__)))."/images/flags/".strtolower($var1[$i][0]).".png")){
                $tmp1="<img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/flags/".strtolower($var1[$i][0]).".png' height='12px' width='18px' />";
            }else{$tmp1="";};
            $tmp=($tmp1!=''?$tmp1:"&nbsp;")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><b>".strtoupper($var1[$i][0])."</b>".($tmp["en_language"]!=''?" ".$tmp["en_language"]." (".$tmp["native_language"].")":"");
            if($var1[$i][0]==''){$all_page--;};
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'> ".($var1[$i][0]!=''?$tmp:"&nbsp;</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><b>".__("Unknown","myStat")."</b>")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Accept_Languages(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total jumps from pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique Accept-Languages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }
};
?>