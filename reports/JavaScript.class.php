<?php

class JavaScript{

    static function getTitle(){
        return __("JavaScript","myStat");
    }

    static function getMenuItemName(){
        return array(__('JavaScript','myStat'),6);
    }

    static function getMenuTreeName(){
        return array(__('System','myStat'),5);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        $var1=$cmn->getSQL("SELECT js,count(DISTINCT ip) FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY js ORDER BY js;");
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==($i/2)?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><b>".($var1[$i][0]!=''?(__("Version of JavaScript","myStat")." ".$var1[$i][0]):__("Unknown","myStat"))."</b><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";
    }

};

?>