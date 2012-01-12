<?php

class Popular_Posts{

    static function getTitle(){
        return __("Popular Posts","myStat");
    }

    static function getMenuItemName(){
        return array(__('Popular Posts','myStat'),1);
    }

    static function getMenuTreeName(){
        return array(__('Pages','myStat'),2);
    }

    function init($date,$page=1){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        $limit=30;$page--;
        $var2=$cmn->getSQL("SELECT sum(count) as sm FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND post_id!=0 GROUP BY post_id;");
        $all_page=count($var2);
        $var1=$cmn->getSQL("SELECT host,uri,title,sum(count) as sm,post_id FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND post_id!=0 GROUP BY post_id ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";");
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Popular_Posts(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var2);$i++){
            $sum+=$var2[$i][0];
            if($max<$var2[$i][0]){$max=$var2[$i][0];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            $post=get_post($var1[$i][4]);
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><a href='".$post->guid."' target='_blank'>".$cmn->my_wordwrap("http://".$var1[$i][0]."/".get_page_uri($var1[$i][4]),5,'<wbr/>')."</a><br/><b>".__("Title:","myStat")."</b> ".$post->post_title."<br/><b>".__("Publish Date","myStat")."</b>: ".$post->post_date."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][3]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($var1[$i][3],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][3]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Popular_Posts(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total requests for pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+2)/2)==($i+2)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique pages addresses","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }
};
?>