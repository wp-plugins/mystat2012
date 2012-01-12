<?php

class Popular_Titles{

    static function getTitle(){
        return __("Popular Titles","myStat");
    }

    static function getMenuItemName(){
        return array(__('Popular Titles','myStat'),3);
    }

    static function getMenuTreeName(){
        return array(__('Pages','myStat'),2);
    }

    function init($date,$page=1,$exp='',$num=0){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        if($exp!=''){
            echo $num.'###';
            $exp=$cmn->unicodeUrlDecode($exp,'UTF-8');
            $var=$cmn->getSQL("SELECT host,uri,sum(count) as sm FROM `%%PREFIX%%myStat_main` WHERE title='".$exp."' AND date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 GROUP by uri ORDER BY sm DESC LIMIT 0,20;");
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='http://".$var[$i][0].$var[$i][1]."' target='_blank'>".$cmn->my_wordwrap("http://".$var[$i][0].$var[$i][1],5,'<wbr/>')."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][2],0,',',' ')."</td></tr>";
            };
            echo "</table>";
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING SUB (report '.$this->getTitle().')');};
            return "NODATE";
            exit();
        };
        $limit=30;$page--;
        $var2=$cmn->getSQL("SELECT sum(count) as sm FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND title!='' GROUP BY title;");
        $all_page=count($var2);
        $var1=$cmn->getSQL("SELECT host,uri,title,sum(count) as sm FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND title!='' GROUP BY title ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";");
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Popular_Titles(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<script>";
        echo "function myStat_expand(num,page){";
        echo "page=page++;";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][2])."';";
        };
        echo "x_Popular_Titles(page,id[num],num,'myStat_expand_load');";
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
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 most popular addresses","myStat")."' title='".__("Show 20 most popular addresses","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.",\"".$page."\");}else{el.style.display=\"none\";};'/> ".$var1[$i][2]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][3]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' valign='top' nowrap><b>".number_format($var1[$i][3],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][3]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Popular_Titles(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
    }
};
?>