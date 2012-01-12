<?php

class Loading_Speed{

    static function getTitle(){
        return __("Loading Speed","myStat");
    }

    static function getMenuItemName(){
        return array(__('Loading Speed','myStat'),4);
    }

    static function getMenuTreeName(){
        return array(__('Audience','myStat'),1);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $var1=$cmn->getSQL("SELECT date_load-date as st FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (date_load!='0000-00-00 00:00:00' or title!='') ORDER by st;");
        $all_sum=0;$max=0;$sum=0;
        for($i=0;$i<count($var1);$i++){
            if($var1[$i][0]>=0 and $var1[$i][0]<1){$cnt[0]++;}
            elseif($var1[$i][0]>=1 and $var1[$i][0]<=2){$cnt[1]++;}
            elseif($var1[$i][0]>=3 and $var1[$i][0]<=4){$cnt[2]++;}
            elseif($var1[$i][0]>=5 and $var1[$i][0]<=6){$cnt[3]++;}
            elseif($var1[$i][0]>=7 and $var1[$i][0]<=8){$cnt[4]++;}
            elseif($var1[$i][0]>=9 and $var1[$i][0]<=10){$cnt[5]++;}
            elseif($var1[$i][0]>=11 and $var1[$i][0]<=15){$cnt[6]++;}
            elseif($var1[$i][0]>=16 and $var1[$i][0]<=20){$cnt[7]++;}
            elseif($var1[$i][0]>=21 and $var1[$i][0]<=30){$cnt[8]++;}
            elseif($var1[$i][0]>=31 and $var1[$i][0]<=45){$cnt[9]++;}
            elseif($var1[$i][0]>=46 and $var1[$i][0]<=60){$cnt[10]++;}
            elseif($var1[$i][0]>60){$cnt[11]++;}
            else{$cnt[11]++;};
            if($var1[$i][0]>=0 and $var1[$i][0]<=300){$sum++;$all_sum+=$var1[$i][0];};
        };
        if(count($cnt)>0){
            $max=max($cnt);
        }else{
            $max=1;
            $sum=1;
        };
        if($sum==0){$sum=1;};
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Less than 1 second","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[0]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[0],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[0]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("1-2 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[1]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("3-4 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[2]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[2],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[2]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("5-6 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[3]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[3],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[3]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("7-8 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[4]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[4],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[4]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("9-10 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[5]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[5],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[5]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("11-15 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[6]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[6],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[6]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("16-20 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[7]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[7],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[7]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("21-30 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[8]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[8],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[8]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("31-45 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[7]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[7],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[7]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("46-60 seconds","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[8]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[8],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[8]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("More than 1 minute","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$cnt[9]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($cnt[9],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$cnt[9]/$sum,2,',',' ')."%</sup></td></tr>";
        echo "</table>";

        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><b>".__("Average time","myStat")."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($all_sum/$sum,2,',',' ')."</b></td></tr>";
        echo "</table>";
    }
};
?>