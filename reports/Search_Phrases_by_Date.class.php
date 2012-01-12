<?php

class Search_Phrases_by_Date{

    static function getTitle(){
        return __("Search Phrases by Date","myStat");
    }

    static function getMenuItemName(){
        return array(__('Search Phrases by Date','myStat'),7);
    }

    static function getMenuTreeName(){
        return array(__('Referrers','myStat'),3);
    }

    function init($date,$page=1){
        include_once(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)).'/../modules/common.class.php');
        $cmn=new myStat_common();
        if(get_option("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        $limit=30;$page--;
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
        $host=$matches[3];
        $type=$GLOBALS['wpdb']->get_results("SELECT value1,value2,value3,value4 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='1';", ARRAY_N);
        $var1=$GLOBALS['wpdb']->get_results("SELECT referer,count,www,host,uri,DATE_FORMAT(date,'%d-%m-%Y %H-%i'),DATE_FORMAT(date,'%d-%m-%Y') FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer!='' AND !(LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) ORDER BY date DESC;", ARRAY_N);
        $k=0;
        for($i=0;$i<count($var1);$i++){
            for($j=0;$j<count($type);$j++){
                if(stripos($var1[$i][0],$type[$j][0])>0){
                    preg_match($type[$j][2],$var1[$i][0],$matches);
                    if(trim($matches[1])!=''){
                        $tt[$k][0]=$type[$j][1];
                        $tt[$k][1]=$var1[$i][1]+0;
                        $tt[$k][2]=$var1[$i][0];
                        $tt[$k][3]=trim($cmn->unicodeUrlDecode($cmn->unicodeUrlDecode(trim($matches[1]),'UTF-8'),'UTF-8'));
                        if($type[$j][3]!='' and trim($type[$j][3])!='"'){
                            $tt[$k][3]=iconv($type[$j][3],'UTF-8',$tt[$k][3]);
                        };
                        $tt[$k][4]="http://".($var1[$i][2]=='yes'?"www.":"").$var1[$i][3].$var1[$i][4];
                        $tt[$k][5]=$var1[$i][5];
                        $tt[$k][6]=$var1[$i][6];
                        $k++;
                        break;
                    };
                };
            };
        };
        $sum=0;
        $all_page=count($tt);
        for($i=0;$i<count($tt);$i++){
            $sum+=$tt[$i][1];
            if($max<$tt[$i][1]){$max=$tt[$i][1];};
        };
        if($sum<1 and count($tt)>0){$sum=1;};
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Search_Phrases_by_Date(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $end=$limit*$page+$limit;if($end>$all_page){$end=$all_page;};
        $dt='';$tmp=0;
        for($i=$limit*$page;$i<$end;$i++){
            if($dt!=$tt[$i][6]){
                echo "<tr".(floor(($i+$tmp)/2)==(($i+$tmp)/2)?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='middle' colspan='3'><b>".($tt[$i][6])."</b></td></tr>";
                $tmp++;
                $dt=$tt[$i][6];
            };
            echo "<tr".(floor(($i+$tmp)/2)==(($i+$tmp)/2)?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($j)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><sup>[ ".$tt[$i][5]." ]</sup> <b>".$tt[$i][0].":</b> <a href=".$tt[$i][2]." target='_blank'>".$tt[$i][3]."</a><br/>&nbsp; <sup><i><b>".__("Page Found","myStat").":</b></i> <a href=".$tt[$i][4]." target='_blank'>".$cmn->my_wordwrap($tt[$i][4],5,'<wbr/>')."</a></sup><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$tt[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($tt[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$tt[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Search_Phrases_by_Date(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
    }
};

?>