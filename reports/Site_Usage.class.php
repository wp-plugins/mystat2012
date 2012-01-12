<?php

class Site_Usage{

    static function getTitle(){
        return __("Site Usage/ by summary table","myStat");
    }

    static function getMenuItemName(){
        return array(__('Site Usage','myStat'),1);
    }

    static function getMenuTreeName(){
        return array(__('Audience','myStat'),1);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        echo "<br/><center><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/mystat.php?act=stat_img&d1=".$date[0]."&d2=".$date[1]."' width='600' height='300' border='0' /></center>";
        $var2=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(*),sum(count),TO_DAYS(date),WEEKDAY(date),DATE_FORMAT(date,'%Y-%m-%d') FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY TO_DAYS(date);");
        if(!is_array($var2)){
            $var2[0][0]=date('d-m-y',strtotime(substr($date[1],0,10)));
            $var2[0][1]=0;
            $var2[0][2]=0;
            $var2[0][3]=1;
            $var2[0][4]=(int)date('w',strtotime(substr($date[1],0,10)));
            $var2[0][5]=substr($date[1],0,10);
        };
        $var11=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(DISTINCT ip),count(*) FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY TO_DAYS(date);");
        $var12=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(DISTINCT ip),count(*) FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);");
        $var13=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(DISTINCT user),count(*) FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user!='' GROUP BY TO_DAYS(date);");
        $var14=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),sum(count) FROM `%%PREFIX%%myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);");
        $count=count($var2);$j=$count-1;
        for($i=0;$i<$count;$i++){
            $table[0][$j]=$var2[$i][0];
            $table[1][$j]=$var2[$i][4];
            $table[2][$j]=$var2[$i][2];
            if(is_array($var11)){
                foreach($var11 as $tt){
                    if($tt[0]==$var2[$i][0]){
                        $table[3][$j]=$tt[1];
                        break;
                    };
                };
            };
            if(!isset($table[3][$j])){$table[3][$j]=0;};
            if(is_array($var12)){
                foreach($var12 as $tt){
                    if($tt[0]==$var2[$i][0]){
                        $table[4][$j]=$tt[1];
                        break;
                    };
                };
            };
            if(!isset($table[4][$j])){$table[4][$j]=0;};
            if(is_array($var13)){
                foreach($var13 as $tt){
                    if($tt[0]==$var2[$i][0]){
                        $table[5][$j]=$tt[1];
                        break;
                    };
                };
            };
            if(!isset($table[5][$j])){$table[5][$j]=0;};
            $table[6][$j]=$var2[$i][5];
            if(is_array($var14)){
                foreach($var14 as $tt){
                    if($tt[0]==$var2[$i][0]){
                        $table[7][$j]=$tt[1];
                        break;
                    };
                };
            };
            if(!isset($table[7][$j])){$table[7][$j]=0;};
            $j--;
        };
        if($count==1){
            $var2=$cmn->getSQL("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(*),sum(count),TO_DAYS(date),WEEKDAY(date),DATE_FORMAT(date,'%Y-%m-%d') FROM `%%PREFIX%%myStat_main` WHERE  TO_DAYS(date) = TO_DAYS('".$table[6][0]."')-1 GROUP BY TO_DAYS(date);");
            $tbl[0][0]=$var2[0][0];
            $tbl[1][0]=$var2[0][4];
            $tbl[2][0]=$var2[0][2];
            $var1=$cmn->getSQL("SELECT count(DISTINCT ip),count(*) FROM `%%PREFIX%%myStat_main` WHERE  TO_DAYS(date)='".$var2[0][3]."';");
            $tbl[3][0]=$var1[0][0];
            $var1=$cmn->getSQL("SELECT count(DISTINCT ip),count(*) FROM `%%PREFIX%%myStat_main` WHERE  TO_DAYS(date)='".$var2[0][3]."' AND (date_load!='0000-00-00 00:00:00' or title!='');");
            $tbl[4][0]=$var1[0][0];
            $var1=$cmn->getSQL("SELECT count(DISTINCT user),count(*) FROM `%%PREFIX%%myStat_main` WHERE  TO_DAYS(date)='".$var2[0][3]."' AND user!='';");
            $tbl[5][0]=$var1[0][0];
            $tbl[6][0]=$var2[0][5];
            $var1=$cmn->getSQL("SELECT sum(count) FROM `%%PREFIX%%myStat_main` WHERE TO_DAYS(date)='".$var2[0][3]."' AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date)");
            $tbl[7][0]=$var1[0][0];
            $var2=$cmn->getSQL("SELECT sum(count) FROM `%%PREFIX%%myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') GROUP BY TO_DAYS(date);");
            $tbl1=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT count(DISTINCT ip) FROM `%%PREFIX%%myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);");
            $tbl2=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT count(DISTINCT ip) FROM `%%PREFIX%%myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') GROUP BY TO_DAYS(date);");
            $tbl3=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT count(DISTINCT user)-1 FROM `%%PREFIX%%myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') GROUP BY TO_DAYS(date);");
            $tbl4=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT sum(count) FROM `%%PREFIX%%myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);");
            $tbl5=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT sum(count) FROM `%%PREFIX%%myStat_main` WHERE date>(now() - INTERVAL 7 DAY) GROUP BY TO_DAYS(date);");
            $tbl11=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT count(DISTINCT ip) FROM `%%PREFIX%%myStat_main` WHERE date>(now() - INTERVAL 7 DAY) AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);");
            $tbl12=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT count(DISTINCT ip) FROM `%%PREFIX%%myStat_main` WHERE date>(now() - INTERVAL 7 DAY) GROUP BY TO_DAYS(date);");
            $tbl13=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT count(DISTINCT user)-1 FROM `%%PREFIX%%myStat_main` WHERE date>(now() - INTERVAL 7 DAY) GROUP BY TO_DAYS(date);");
            $tbl14=$cmn->avg($var2);
            $var2=$cmn->getSQL("SELECT sum(count) FROM `%%PREFIX%%myStat_main` WHERE date>(now() - INTERVAL 7 DAY) AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);");
            $tbl15=$cmn->avg($var2);
            
            echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            echo "<tr align='center' valign='middle' style='background-color:#E6E6E6;'><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'>&nbsp;</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".$table[0][0].", ";
            switch ($table[1][0]){
                case 0:_e("Monday","myStat");break;
                case 1:_e("Tuesday","myStat");break;
                case 2:_e("Wednesday","myStat");break;
                case 3:_e("Thursday","myStat");break;
                case 4:_e("Friday","myStat");break;
                case 5:_e("Saturday","myStat");break;
                case 6:_e("Sunday","myStat");break;
            };
            if($tbl[0][0]!=''){
                echo "</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".$tbl[0][0].", ";
                switch ($tbl[1][0]){
                    case 0:_e("Monday","myStat");break;
                    case 1:_e("Tuesday","myStat");break;
                    case 2:_e("Wednesday","myStat");break;
                    case 3:_e("Thursday","myStat");break;
                    case 4:_e("Friday","myStat");break;
                    case 5:_e("Saturday","myStat");break;
                    case 6:_e("Sunday","myStat");break;
                };
                echo "</td>";
            };
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Average by","myStat").", ";
            switch ($table[1][0]){
                case 0:_e("Monday","myStat");break;
                case 1:_e("Tuesday","myStat");break;
                case 2:_e("Wednesday","myStat");break;
                case 3:_e("Thursday","myStat");break;
                case 4:_e("Friday","myStat");break;
                case 5:_e("Saturday","myStat");break;
                case 6:_e("Sunday","myStat");break;
            };
            echo "</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Average by","myStat")." ".__("7 days","myStat")."</b></td></tr>";
            echo "<tr><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Login Users","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[5][0],0,',',' ')."</td>".($tbl[0][0]!=''?"<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[5][0],0,',',' ')."</td>":"")."<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl4,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl14,1,',',' ')."</td></tr>";
            echo "<tr style='background-color:#E6E6E6'><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Users","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[4][0],0,',',' ')."</td>".($tbl[0][0]!=''?"<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[4][0],0,',',' ')."</td>":"")."<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl2,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl12,1,',',' ')."</td></tr>";
            echo "<tr><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Hosts","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[3][0],0,',',' ')."</td>".($tbl[0][0]!=''?"<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[3][0],0,',',' ')."</td>":"")."<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl3,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl13,1,',',' ')."</td></tr>";
            echo "<tr style='background-color:#E6E6E6'><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Pageviews","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[2][0],0,',',' ')."</td>".($tbl[0][0]!=''?"<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[2][0],0,',',' ')."</td>":"")."<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl1,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl11,1,',',' ')."</td></tr>";
            echo "<tr><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Pageviews by users","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[7][0],0,',',' ')."</td>".($tbl[0][0]!=''?"<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[7][0],0,',',' ')."</td>":"")."<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl5,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl15,1,',',' ')."</td></tr>";
            echo "</tr>";
            echo "</table>";

        }else{
            echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            echo "<tr align='center' valign='middle' style='background-color:#E6E6E6;'><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Date","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Pageviews","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Hosts","myStat")."</b></td><td style='font-size:11px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Users","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Login Users","myStat")."</b></td></tr>";
            $sum=$cmn->getSQLONE("SELECT sum(count) FROM `%%PREFIX%%myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1;");
            $sum_r=$cmn->getSQLONE("SELECT sum(count) FROM `%%PREFIX%%myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1 AND (date_load!='0000-00-00 00:00:00' or title!='');");
            $m_sum=$table[2][0]-$sum;
            $r_sum=$table[7][0]-$sum_r;
            $sum=$cmn->getSQLONE("SELECT count(DISTINCT ip) FROM `%%PREFIX%%myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1;");
            $m_sum1=$table[3][0]-$sum;
            $sum=$cmn->getSQLONE("SELECT count(DISTINCT ip) FROM `%%PREFIX%%myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1 AND (date_load!='0000-00-00 00:00:00' or title!='');");
            $m_sum2=$table[4][0]-$sum;
            $sum=$cmn->getSQLONE("SELECT count(DISTINCT user) FROM `%%PREFIX%%myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1 AND user!='';");
            $m_sum3=$table[5][0]-$sum;
            for($i=0;$i<$count;$i++){
                echo "<tr onclick=\"myStat_date_form.date1.value='".$table[6][$i]." 00:00:00';myStat_date_form.date2.value='".$table[6][$i]." 23:59:59';myStat_date_form.submit();\" onmouseover=\"this.style.backgroundColor='#A0B0FF';\" onmouseout=\"this.style.backgroundColor='".((floor($i/2)!=($i/2))?"#E6E6E6":"")."';\" style='".((floor($i/2)!=($i/2))?" background-color:#E6E6E6;":"")."cursor:pointer;'>";
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".($i==0?"color:blue;":(($table[1][$i]==5 or $table[1][$i]==6)?"color:red;":""))."' nowrap>&nbsp; ".$table[0][$i].", ";
                switch ($table[1][$i]){
                    case 0:_e("Monday","myStat");break;
                    case 1:_e("Tuesday","myStat");break;
                    case 2:_e("Wednesday","myStat");break;
                    case 3:_e("Thursday","myStat");break;
                    case 4:_e("Friday","myStat");break;
                    case 5:_e("Saturday","myStat");break;
                    case 6:_e("Sunday","myStat");break;
                };
                echo "</td>";
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".(max($table[2])==$table[2][$i]?"color:blue;font-weight:bold;":((min($table[2])==$table[2][$i] and $i>0)?"color:red;font-weight:bold;":""))."' align='center'>".number_format($table[2][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($m_sum>0?"+":"").number_format($m_sum,0,',',' ').")</sup>":"")." / ".number_format($table[7][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($r_sum>0?"+":"").number_format($r_sum,0,',',' ').")</sup>":"")."</td>";
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".(max($table[3])==$table[3][$i]?"color:blue;font-weight:bold;":((min($table[3])==$table[3][$i] and $i>0)?"color:red;font-weight:bold;":""))."' align='center'>".number_format($table[3][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($m_sum1>0?"+":"").number_format($m_sum1,0,',',' ').")</sup>":"")."</td>";
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".(max($table[4])==$table[4][$i]?"color:blue;font-weight:bold;":((min($table[4])==$table[4][$i] and $i>0)?"color:red;font-weight:bold;":""))."' align='center'>".number_format($table[4][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($m_sum2>0?"+":"").number_format($m_sum2,0,',',' ').")</sup>":"")."</td>";
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".(max($table[5])==$table[5][$i]?"color:blue;font-weight:bold;":((min($table[5])==$table[5][$i] and $i>0)?"color:red;font-weight:bold;":""))."' align='center'>".number_format($table[5][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($m_sum3>0?"+":"").number_format($m_sum3,0,',',' ').")</sup>":"")."</td>";
                echo "</tr>";
            };
            echo "<tr".(floor($i/2)!=$i/2?" style='background-color:#E6E6E6'":"").">";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".__("Average","myStat")."</b></td>";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".number_format(is_array($table[2])?(array_sum($table[2])/count($table[2])):0,1,',',' ')." / ".number_format(is_array($table[7])?(array_sum($table[7])/count($table[7])):0,1,',',' ')."</b></td>";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".number_format(is_array($table[3])?(array_sum($table[3])/count($table[3])):0,1,',',' ')."</b></td>";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".number_format(is_array($table[4])?(array_sum($table[4])/count($table[4])):0,1,',',' ')."</b></td>";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".number_format(is_array($table[5])?(array_sum($table[5])/count($table[5])):0,1,',',' ')."</b></td>";
            echo "</tr>";
            echo "<tr".(floor(($i+1)/2)!=($i+1)/2?" style='background-color:#E6E6E6'":"").">";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".__("Total","myStat")."</b></td>";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".number_format(is_array($table[2])?array_sum($table[2]):0,0,',',' ')." / ".number_format(is_array($table[7])?array_sum($table[7]):0,0,',',' ')."</b></td>";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".number_format(is_array($table[3])?array_sum($table[3]):0,0,',',' ')."</b></td>";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".number_format(is_array($table[4])?array_sum($table[4]):0,0,',',' ')."</b></td>";
            echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'><b>".number_format(is_array($table[5])?array_sum($table[5]):0,0,',',' ')."</b></td>";
            echo "</tr>";
            echo "</table>";
        };
    }
};
?>