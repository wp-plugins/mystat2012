<?php

class Popular_Pages_404{

    static function getTitle(){
        return __("Popular Pages 404","myStat");
    }

    static function getMenuItemName(){
        return array(__('Popular Pages 404','myStat'),5);
    }

    static function getMenuTreeName(){
        return array(__('Pages','myStat'),2);
    }

    function init($date,$page=1,$exp='',$num=0){
        include_once(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/../modules/common.class.php');
        $cmn=new myStat_common();
        if(get_option("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        if($exp!=''){
            echo $num.'###';
            preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
            $host=$matches[3];
            $var=$GLOBALS['wpdb']->get_results("SELECT referer,count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE uri='".$exp."' AND date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 AND (LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) GROUP BY referer ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='".$var[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var[$i][0], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][1],0,',',' ')."</td></tr>";
            };
            if(count($var)==0){echo "<tr><td>".__("No URL","myStat")."</td></tr>";};
            echo "</table>";
            if(get_option("myStat_debug")==1){$cmn->setDebug('LOADING SUB (report '.$this->getTitle().')');};
            return "NODATE";
            exit();
        };
        $limit=30;$page--;
        $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 GROUP BY uri;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT host,uri,title,sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 GROUP BY uri ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Popular_Pages_404(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<script>";
        echo "function myStat_expand(num,page){";
        echo "page=page++;";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][1])."';";
        };
        echo "x_Popular_Pages_404(page,id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var2);$i++){
            $sum+=$var2[$i][0];
            if($max<$var2[$i][0]){$max=$var2[$i][0];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 pages of your site which refer to this","myStat")."' title='".__("Show 20 pages of your site which refer to this","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.",\"".$page."\");}else{el.style.display=\"none\";};'/> <a href='http://".$var1[$i][0].$var1[$i][1]."' target='_blank'>".$cmn->my_wordwrap("http://".$var1[$i][0].$var1[$i][1],5,'<wbr/>')."</a><br/>".$var1[$i][2]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][3]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][3],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][3]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Popular_Pages_404(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total requests for pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique pages addresses","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }
};
?>