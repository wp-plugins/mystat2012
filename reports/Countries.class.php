<?php

class Countries{

    function getTitle(){
        return __("Countries","myStat");
    }

    function getMenuItemName(){
        return array(__('Countries','myStat'),2);
    }

    function getMenuTreeName(){
        return array(__('Geography','myStat'),4);
    }

    function init($date,$page=1){
        $limit=30;$page--;
        $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY country;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT country,count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY country ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/../modules/common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Countries(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var2);$i++){
            $sum+=$var2[$i][0];
            if($max<$var2[$i][0]){$max=$var2[$i][0];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            if($var1[$i][0]==''){$var1[$i][0]=__("Not defined","myStat");};
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'> ".$var1[$i][0]."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Countries(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND country!='' GROUP BY country;", ARRAY_N);
        $all_page=count($var2);
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique countries","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }
};
?>