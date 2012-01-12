<?php

class Screen_Resolution{

    static function getTitle(){
        return __("Screen Resolution","myStat");
    }

    static function getMenuItemName(){
        return array(__('Screen Resolution','myStat'),4);
    }

    static function getMenuTreeName(){
        return array(__('System','myStat'),5);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        $var1=$cmn->getSQL("SELECT screen,count(screen) as sm FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND screen!='' AND screen!='0x0' GROUP BY screen ORDER BY sm DESC;");
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        if(count($var1)>=4){$cc=4;}else{$cc=count($var1);};
        $xz=0;$data=array();$label=array();
        for($i=0;$i<=$cc;$i++){
            $label[]=$var1[$i][0];
            $data[]=$var1[$i][1];
            $xz+=$var1[$i][1];
        };
        if(count($var1)>4){
            $data[]=($sum-$xz);
            $label[]=__("Other resolution","myStat");
        };
        if(count($var1)>0){echo "<br/><center>".$cmn->chart_html("p3",600,200,$data,$label)."</center>";};
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'> ".$var1[$i][0]."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $all_page=$cmn->getSQLONE("SELECT count(screen) as sm FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (screen='' OR screen='0x0') AND (date_load!='0000-00-00 00:00:00' or title!='');");
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Unknown","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($all_page,0,',',' ')."</b></td></tr>";
        echo "</table>";
    }
};
?>