<?php

class Pageviews_per_User{

    static function getTitle(){
        return __("Pageviews per User","myStat");
    }

    static function getMenuItemName(){
        return array(__('Pageviews per User','myStat'),3);
    }

    static function getMenuTreeName(){
        return array(__('Audience','myStat'),1);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $var1=$cmn->getSQL("SELECT sum(count) as st, ip FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (date_load!='0000-00-00 00:00:00' or title!='')  GROUP BY ip ORDER by st;");
        $all_sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            if($var1[$i][0]==1){$cnt[0]++;}
            elseif($var1[$i][0]==2){$cnt[1]++;}
            elseif($var1[$i][0]==3){$cnt[2]++;}
            elseif($var1[$i][0]==4){$cnt[3]++;}
            elseif($var1[$i][0]==5){$cnt[4]++;}
            elseif($var1[$i][0]>5 and $var1[$i][0]<10){$cnt[5]++;}
            elseif($var1[$i][0]>=10 and $var1[$i][0]<=20){$cnt[6]++;}
            elseif($var1[$i][0]>20 and $var1[$i][0]<=50){$cnt[7]++;}
            elseif($var1[$i][0]>50 and $var1[$i][0]<=100){$cnt[8]++;}
            elseif($var1[$i][0]>100){$cnt[9]++;};
            $all_sum+=$var1[$i][0];
        };
        if(count($cnt)>0){
            $max=max($cnt);
            $sum=array_sum($cnt);
        }else{
            $max=1;
            $sum=1;
        };
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("1 page","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[0]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[0],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[0]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("2 pages","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[1]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("3 pages","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[2]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[2],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[2]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("4 pages","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[3]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[3],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[3]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("5 pages","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[4]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[4],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[4]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".sprintf(__("%s to %s pages","myStat"),'6','9')."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[5]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[5],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[5]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".sprintf(__("%s to %s pages","myStat"),'10','20')."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[6]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[6],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[6]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".sprintf(__("%s to %s pages","myStat"),'21','50')."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[7]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[7],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[7]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".sprintf(__("%s to %s pages","myStat"),'51','100')."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[8]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[8],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[8]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("more than 100 pages","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[9]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[9],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[9]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "</table>";

        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><b>".__("Average depth","myStat")."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($all_sum/$sum,2,',',' ')."</b></td></tr>";
        echo "</table>";
    }

};

?>