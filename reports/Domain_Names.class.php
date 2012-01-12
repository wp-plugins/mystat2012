<?php

class Domain_Names{

    static function getTitle(){
        return __("Domain Names","myStat");
    }

    static function getMenuItemName(){
        return array(__('Domain Names','myStat'),4);
    }

    static function getMenuTreeName(){
        return array(__('Pages','myStat'),2);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $var1=$cmn->getSQL("SELECT host,count(www) FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND www='no' GROUP BY host;");
        $var2=$cmn->getSQL("SELECT host,count(www) FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND www='yes' GROUP BY host;");
        $max=0;$sum=0;$count=0;
        for($i=0;$i<count($var1);$i++){
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
            $sum+=$var1[$i][1];
            $count++;
        };
        for($i=0;$i<count($var2);$i++){
            if($max<$var2[$i][1]){$max=$var2[$i][1];};
            $sum+=$var2[$i][1];
            $count++;
        };
        $j=0;
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($j/2)==$j/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='middle'><b>".($j+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:left;' valign='top' width='100%'><a href='http://".$var1[$i][0]."' target='_blank'>http://".$var1[$i][0]."</a><br/><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        for($i=0;$i<count($var2);$i++){
            echo "<tr".(floor($j/2)==$j/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='middle'><b>".($j+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:left;' valign='top' width='100%'><a href='http://www.".$var2[$i][0]."' target='_blank'>http://www.".$var2[$i][0]."</a><br/><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var2[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var2[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var2[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
    }

};

?>