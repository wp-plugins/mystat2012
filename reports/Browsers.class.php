<?php

class Browsers{

    static function getTitle(){
        return __("Browsers","myStat");
    }

    static function getMenuItemName(){
        return array(__('Browsers','myStat'),5);
    }

    static function getMenuTreeName(){
        return array(__('System','myStat'),5);
    }

    function init($date,$page=1,$max=0){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        $limit=30;$page--;
        $all_page=$cmn->getSQLONE("SELECT count(DISTINCT user_agent) FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title!='' OR date_load!='0000-00-00 00:00:00');");

        $type=$cmn->getSQL("SELECT value1,value2 FROM %%PREFIX%%myStat_data WHERE type='5' ORDER BY value3;");
        $limit=$all_page;
        $var1=$cmn->getSQL("SELECT user_agent,count(user_agent) as sm FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title!='' OR date_load!='0000-00-00 00:00:00') GROUP BY user_agent ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";");
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
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Browsers(\'[page_number]\','.$max.',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        if(count($new)>=4){$cc=4;}else{$cc=count($new);};
        reset($new);$i=0;$xz=0;$data=$label=array();
        while($fn = current($new)){
            $i++;
            $label[]=key($new);
            $data[]=$fn;
            $xz+=$fn;
            next($new);
            if($i>$cc){break;};
        };
        if(count($new)>4){
            $label[]=__("Other browsers","myStat");
            $data[]=($sum-$xz);
        };
        if(count($new)>0){echo "<br/><center>".$cmn->chart_html("p3",600,200,$data,$label)."</center>";};
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $i=0;reset($new);
        while($fn = current($new)){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'> ";
            echo key($new);
            echo "<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$fn/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($fn,0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$fn/$sum,2,',',' ')."%</sup></td></tr>";
            $i++;
            next($new);
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_Browsers(\'[page_number]\','.$max.',\'myStat_load\');',$all_page,$tmp,$limit);
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Browsers","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other browsers","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($no_detect,0,',',' ')."</td></tr>";
        echo "</table>";
    }
};
?>