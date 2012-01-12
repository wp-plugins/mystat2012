<?php
class myStat_main{

    function head_page($name,$page=''){
    $test_var='';
        if($_GET['date1']!='' and $_GET['date2']!=''){
            $var[0][0]=substr($_GET['date1'],0,10);
            $var[0][1]=substr($_GET['date2'],0,10);
        }else{
            $var=$GLOBALS['wpdb']->get_results("SELECT DATE_FORMAT(now() - INTERVAL 1 MONTH,'%Y-%m-%d'),DATE_FORMAT(max(date),'%Y-%m-%d'),DATE_FORMAT(now() - INTERVAL 1 DAY,'%Y-%m-%d'),DATE_FORMAT(now() - INTERVAL 1 WEEK,'%Y-%m-%d'),DATE_FORMAT(min(date),'%Y-%m-%d'),DATE_FORMAT(now() - INTERVAL WEEKDAY(now()) DAY,'%Y-%m-%d'),DATE_FORMAT(now(),'%Y-%m-%d') FROM ".$GLOBALS['wpdb']->prefix."myStat_main;", ARRAY_N);
            if($var[0][1]==null){$var[0][1]=$var[0][6];};
            $test_var=$var;
        };
        if($name!=''){
            if(!is_array($test_var)){
                $test_var=$GLOBALS['wpdb']->get_results("SELECT DATE_FORMAT(max(date) - INTERVAL 1 MONTH,'%Y-%m-%d'),DATE_FORMAT(max(date),'%Y-%m-%d'),DATE_FORMAT(max(date) - INTERVAL 1 DAY,'%Y-%m-%d'),DATE_FORMAT(max(date) - INTERVAL 1 WEEK,'%Y-%m-%d'),DATE_FORMAT(min(date),'%Y-%m-%d'),DATE_FORMAT(max(date) - INTERVAL WEEKDAY(max(date)) DAY,'%Y-%m-%d') FROM ".$GLOBALS['wpdb']->prefix."myStat_main;", ARRAY_N);
            };
            echo "<form name='myStat_date_form' method='get'><input type='hidden' name='page' value='".dirname(dirname(plugin_basename(__FILE__)))."/mystat.php' />";
            echo "<table width='100%' border='0' style='padding:0;margin:0'>";
            echo "<tr><td nowrap style='padding-right:5px'><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/report_description.gif' width='12px' height='14px' /></td><td nowrap width='100%'><b>".$name."</b></td></tr>";
            echo "<tr><td>&nbsp;</td><td><sup>".sprintf(__("The report for the period from %s to %s","myStat"),"<input name='date1'onclick=\"A_TCALS['myStat_d1'].f_toggle();\" id='tcalico_myStat_d1' type='text' readonly style='text-align:center;font-size:10px;font-weight:bold;cursor:pointer;border:1px silver solid;color:black;background-color:#DDDDDD;margin:3px;padding:3px;-moz-border-radius:4px;' value='".$var[0][0]." 00:00:00' />","<input name='date2' onclick=\"A_TCALS['myStat_d2'].f_toggle();\" id='tcalico_myStat_d2' type='text' readonly style='font-size:10px;font-weight:bold;cursor:pointer;border:1px silver solid;color:black;background-color:#DDDDDD;margin:3px;padding:3px;-moz-border-radius:4px;text-align:center;' value='".$var[0][1]." 23:59:59' />")."<b onclick=\"myStat_date_form.submit();\" style='cursor:pointer;border:1px silver solid;color:black;background-color:#DDDDDD;margin:3px;padding:3px;-moz-border-radius:4px;'>".__("OK","myStat")."</b></sup></td></tr>";
            echo "<tr><td>&nbsp;</td><td nowrap align='center'><sup>[ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][4]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][4] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("All","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][1]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][1] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("Today","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][2]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][2]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][2] and $var[0][1]==$test_var[0][2])?"color:red;text-decoration:underline;":"")."'>".__("Yesterday","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][5]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][5] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("This Week","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".substr($test_var[0][1],0,8)."01 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==substr($test_var[0][1],0,8).'01' and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("This Month","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][3]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][3] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("Last 7 Days","myStat")."</b> ] [ <b onclick=\"myStat_date_form.date1.value='".$test_var[0][0]." 00:00:00';myStat_date_form.date2.value='".$test_var[0][1]." 23:59:59';myStat_date_form.submit();\" style='cursor:pointer;".(($var[0][0]==$test_var[0][0] and $var[0][1]==$test_var[0][1])?"color:red;text-decoration:underline;":"")."'>".__("Last 30 Days","myStat")."</b> ]</sup></td></tr>";
            echo "</table>";
            echo "<input type='hidden' name='myStat_page' value='".$page."' /></form>";
            echo "<script>";
            echo "new tcal ({'formname':'myStat_date_form','controlname':'date1','id':'myStat_d1'});new tcal ({'formname':'myStat_date_form','controlname':'date2','id':'myStat_d2'});";
            echo "</script>";

        };
        return array($var[0][0]." 00:00:00",$var[0][1]." 23:59:59");
    }

###############################################################################################################################

    function page_Site_Usage(){
        $date=$this->head_page(__("Site Usage/ by summary table","myStat"),'page_Site_Usage');
        echo "<br/><center><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/mystat.php?act=stat_img&d1=".$date[0]."&d2=".$date[1]."' width='600' height='300' border='0' /></center>";
        $var2=$GLOBALS['wpdb']->get_results("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(*),sum(count),TO_DAYS(date),WEEKDAY(date),DATE_FORMAT(date,'%Y-%m-%d') FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE  date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY TO_DAYS(date);", ARRAY_N);
        $count=count($var2);$j=$count-1;
        for($i=0;$i<$count;$i++){
            $table[0][$j]=$var2[$i][0];
            $table[1][$j]=$var2[$i][4];
            $table[2][$j]=$var2[$i][2];
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)='".$var2[$i][3]."';", ARRAY_N);
            $table[3][$j]=$var1[0][0];
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)='".$var2[$i][3]."' AND (date_load!='0000-00-00 00:00:00' or title!='');", ARRAY_N);
            $table[4][$j]=$var1[0][0];
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT user),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)='".$var2[$i][3]."' AND user!='';", ARRAY_N);
            $table[5][$j]=$var1[0][0];
            $table[6][$j]=$var2[$i][5];
            $var1=$GLOBALS['wpdb']->get_results("SELECT sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)='".$var2[$i][3]."' AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date)", ARRAY_N);
            $table[7][$j]=$var1[0][0];
            $j--;
        };
        if($count==1){
            $var2=$GLOBALS['wpdb']->get_results("SELECT DATE_FORMAT(date,'%d-%m-%y'),count(*),sum(count),TO_DAYS(date),WEEKDAY(date),DATE_FORMAT(date,'%Y-%m-%d') FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE  TO_DAYS(date) = TO_DAYS('".$table[6][0]."')-1 GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl[0][0]=$var2[0][0];
            $tbl[1][0]=$var2[0][4];
            $tbl[2][0]=$var2[0][2];
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE  TO_DAYS(date)='".$var2[0][3]."';", ARRAY_N);
            $tbl[3][0]=$var1[0][0];
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE  TO_DAYS(date)='".$var2[0][3]."' AND (date_load!='0000-00-00 00:00:00' or title!='');", ARRAY_N);
            $tbl[4][0]=$var1[0][0];
            $var1=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT user),count(*) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE  TO_DAYS(date)='".$var2[0][3]."' AND user!='';", ARRAY_N);
            $tbl[5][0]=$var1[0][0];
            $tbl[6][0]=$var2[0][5];
            $var1=$GLOBALS['wpdb']->get_results("SELECT sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TO_DAYS(date)='".$var2[0][3]."' AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date)", ARRAY_N);
            $tbl[7][0]=$var1[0][0];
            include_once('common.class.php');
            $cmn=new myStat_common();
            $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl1=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl2=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl3=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT user)-1 FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl4=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE WEEKDAY(date) = WEEKDAY('".$table[6][0]." 00:00:00') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl5=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date>(now() - INTERVAL 7 DAY) GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl11=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date>(now() - INTERVAL 7 DAY) AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl12=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date>(now() - INTERVAL 7 DAY) GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl13=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT user)-1 FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date>(now() - INTERVAL 7 DAY) GROUP BY TO_DAYS(date);", ARRAY_N);
            $tbl14=$cmn->avg($var2);
            $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date>(now() - INTERVAL 7 DAY) AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY TO_DAYS(date);", ARRAY_N);
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
            echo "</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Average by","myStat").", ";
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
            echo "<tr><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Login Users","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[5][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[5][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl4,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl14,1,',',' ')."</td></tr>";
            echo "<tr style='background-color:#E6E6E6'><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Users","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[4][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[4][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl2,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl12,1,',',' ')."</td></tr>";
            echo "<tr><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Hosts","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[3][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[3][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl3,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl13,1,',',' ')."</td></tr>";
            echo "<tr style='background-color:#E6E6E6'><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Pageviews","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[2][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[2][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl1,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl11,1,',',' ')."</td></tr>";
            echo "<tr><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='left'>&nbsp; <b>".__("Pageviews by users","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($table[7][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl[7][0],0,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl5,1,',',' ')."</td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".number_format($tbl15,1,',',' ')."</td></tr>";
            echo "</tr>";
            echo "</table>";

        }else{
            echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            echo "<tr align='center' valign='middle' style='background-color:#E6E6E6;'><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Date","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Pageviews","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Hosts","myStat")."</b></td><td style='font-size:11px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Users","myStat")."</b></td><td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;'><b>".__("Login Users","myStat")."</b></td></tr>";
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
                $sum=$GLOBALS['wpdb']->get_var("SELECT sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1;");
                $sum_r=$GLOBALS['wpdb']->get_var("SELECT sum(count) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1 AND (date_load!='0000-00-00 00:00:00' or title!='');");
                $m_sum=$table[2][$i]-$sum;
                $r_sum=$table[7][$i]-$sum_r;
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".(max($table[2])==$table[2][$i]?"color:blue;font-weight:bold;":((min($table[2])==$table[2][$i] and $i>0)?"color:red;font-weight:bold;":""))."' align='center'>".number_format($table[2][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($m_sum>0?"+":"").number_format($m_sum,0,',',' ').")</sup>":"")." / ".number_format($table[7][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($r_sum>0?"+":"").number_format($r_sum,0,',',' ').")</sup>":"")."</td>";
                $sum=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1;");
                $m_sum=$table[3][$i]-$sum;
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".(max($table[3])==$table[3][$i]?"color:blue;font-weight:bold;":((min($table[3])==$table[3][$i] and $i>0)?"color:red;font-weight:bold;":""))."' align='center'>".number_format($table[3][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($m_sum>0?"+":"").number_format($m_sum,0,',',' ').")</sup>":"")."</td>";
                $sum=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1 AND (date_load!='0000-00-00 00:00:00' or title!='');");
                $m_sum=$table[4][$i]-$sum;
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".(max($table[4])==$table[4][$i]?"color:blue;font-weight:bold;":((min($table[4])==$table[4][$i] and $i>0)?"color:red;font-weight:bold;":""))."' align='center'>".number_format($table[4][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($m_sum>0?"+":"").number_format($m_sum,0,',',' ').")</sup>":"")."</td>";
                $sum=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT user) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE TIME(date)<TIME(now()) AND TO_DAYS(date)=TO_DAYS('".$date[1]."')-1 AND user!='';");
                $m_sum=$table[5][$i]-$sum;
                echo "<td style='font-size:11px;height:25px;border:solid #DDDDDD 1px;".(max($table[5])==$table[5][$i]?"color:blue;font-weight:bold;":((min($table[5])==$table[5][$i] and $i>0)?"color:red;font-weight:bold;":""))."' align='center'>".number_format($table[5][$i],0,',',' ').($i==0?" <sup style='color:silver'>(".($m_sum>0?"+":"").number_format($m_sum,0,',',' ').")</sup>":"")."</td>";
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

###############################################################################################################################

    function page_Pageviews_per_Host(){
        $date=$this->head_page(__("Pageviews per Host","myStat"),'page_Pageviews_per_Host');
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $var1=$GLOBALS['wpdb']->get_results("SELECT sum(count) as st, ip FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY ip ORDER by st;", ARRAY_N);
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

###############################################################################################################################

    function page_Popular_Pages($page=1){
        $date=$this->head_page(__("Popular Pages","myStat"),'page_Popular_Pages');
        $limit=30;$page--;
        $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 GROUP BY uri;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT host,uri,title,sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 GROUP BY uri ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Popular_Pages(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var2);$i++){
            $sum+=$var2[$i][0];
            if($max<$var2[$i][0]){$max=$var2[$i][0];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><a href='http://".$var1[$i][0].$var1[$i][1]."' target='_blank'>".$cmn->my_wordwrap("http://".$var1[$i][0].$var1[$i][1],5,'<wbr/>')."</a><br/>".$var1[$i][2]."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][3]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($var1[$i][3],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][3]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Popular_Pages(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total requests for pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+2)/2)==($i+2)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique pages addresses","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function page_Popular_Titles($page=1,$exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            $exp=$cmn->unicodeUrlDecode($exp,'UTF-8');
            $date=$this->head_page("");
            $var=$GLOBALS['wpdb']->get_results("SELECT host,uri,sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE title='".$exp."' AND date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 GROUP by uri ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='http://".$var[$i][0].$var[$i][1]."' target='_blank'>".$cmn->my_wordwrap("http://".$var[$i][0].$var[$i][1],5,'<wbr/>')."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][2],0,',',' ')."</td></tr>";
            };
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Popular Titles","myStat"),'page_Popular_Titles');
        $limit=30;$page--;
        $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND title!='' GROUP BY title;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT host,uri,title,sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND title!='' GROUP BY title ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Popular_Titles(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<script>";
        echo "function myStat_expand(num,page){";
        echo "page=page++;";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][2])."';";
        };
        echo "x_page_Popular_Titles(page,id[num],num,'myStat_expand_load');";
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
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Popular_Titles(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);


    }

###############################################################################################################################

    function page_Referrers($page=1,$exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
#            $exp=$cmn->unicodeUrlDecode($exp,'UTF-8');
            $date=$this->head_page("");
            $var=$GLOBALS['wpdb']->get_results("SELECT host,uri,count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE referer='".$exp."' AND date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 GROUP BY referer ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='http://".$var[$i][0].$var[$i][1]."' target='_blank'>".$cmn->my_wordwrap("http://".$var[$i][0].$var[$i][1], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][2],0,',',' ')."</td></tr>";
            };
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Referrers ","myStat"),'page_Referrers');
        $limit=30;$page--;
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
        $host=$matches[3];
        $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND !(LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) GROUP BY referer;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT referer,count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND !(LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) GROUP BY referer ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Referrers(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<script>";
        echo "function myStat_expand(num,page){";
        echo "page=page++;";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_page_Referrers(page,id[num],num,'myStat_expand_load');";
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
            if($var1[$i][0]!=''){
                echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 most popular target pages, visited from the current one","myStat")."' title='".__("Show 20 most popular target pages, visited from the current one","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.",\"".$page."\");}else{el.style.display=\"none\";};'/> <a href='".$var1[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var1[$i][0],5,"<wbr/>")."</a><div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' valign='top' nowrap><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            }else{
                echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Direct Jump","myStat")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' valign='top' nowrap><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            };
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Referrers(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

    }

###############################################################################################################################

    function page_IP_Addresses($page=1,$exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            echo "<tr><td>".$cmn->whois(long2ip($exp))."</td></tr>";
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("IP Addresses","myStat"),'page_IP_Addresses');
        $limit=30;$page--;

        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200;");
        $var1=$GLOBALS['wpdb']->get_results("SELECT ip,sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 GROUP BY ip ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_IP_Addresses(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<script>";
        echo "function myStat_expand(num,page){";
        echo "page=page++;";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_page_IP_Addresses(page,id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("IP-address info","myStat")."' title='".__("IP-address info","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.",\"".$page."\");}else{el.style.display=\"none\";};'/> ".long2ip($var1[$i][0])."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_IP_Addresses(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

    }

###############################################################################################################################

    function page_Popular_Pages_404($page=1,$exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
            $host=$matches[3];
            $date=$this->head_page("");
            $var=$GLOBALS['wpdb']->get_results("SELECT referer,count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE uri='".$exp."' AND date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 AND (LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) GROUP BY referer ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='".$var[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var[$i][0], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][1],0,',',' ')."</td></tr>";
            };
            if(count($var)==0){echo "<tr><td>".__("No URL","myStat")."</td></tr>";};
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Popular Pages 404","myStat"),'page_Popular_Pages_404');
        $limit=30;$page--;
        $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 GROUP BY uri;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT host,uri,title,sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 GROUP BY uri ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Popular_Pages_404(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<script>";
        echo "function myStat_expand(num,page){";
        echo "page=page++;";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][1])."';";
        };
        echo "x_page_Popular_Pages_404(page,id[num],num,'myStat_expand_load');";
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
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 pages of your site which refer to this","myStat")."' title='".__("Show 20 pages of your site which refer to this","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.",\"".$page."\");}else{el.style.display=\"none\";};'/> <a href='http://".$var1[$i][0].$var1[$i][1]."' target='_blank'>".$cmn->my_wordwrap("http://".$var1[$i][0].$var1[$i][1],5,'<wbr/>')."</a><br/>".$var1[$i][2]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][3]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][3],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][3]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Popular_Pages_404(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total requests for pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique pages addresses","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function page_Links_to_Pages_404($page=1,$exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
            $host=$matches[3];
            $date=$this->head_page("");
            $var=$GLOBALS['wpdb']->get_results("SELECT host,uri,count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 AND referer='".$exp."' GROUP BY uri ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='http://".$var[$i][0].$var[$i][1]."' target='_blank'>".$cmn->my_wordwrap("http://".$var[$i][0].$var[$i][1], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][2],0,',',' ')."</td></tr>";
            };
            if(count($var)==0){echo "<tr><td>".__("No URL","myStat")."</td></tr>";};
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Links to Pages 404","myStat"),'page_Links_to_Pages_404');
        $limit=30;$page--;
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
        $host=$matches[3];
        $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 AND referer!='' AND !(LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) GROUP BY referer;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT referer,sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=404 AND referer!='' AND !(LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) GROUP BY referer ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Links_to_Pages_404(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<script>";
        echo "function myStat_expand(num,page){";
        echo "page=page++;";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_page_Links_to_Pages_404(page,id[num],num,'myStat_expand_load');";
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
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 most popular target pages, visited from the current one","myStat")."' title='".__("Show 20 most popular target pages, visited from the current one","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.",\"".$page."\");}else{el.style.display=\"none\";};'/> <a href='".$var1[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var1[$i][0],5,'<wbr/>')."</a><div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Links_to_Pages_404(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total jumps from pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique referrers","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function page_Jumps_from_Search_Engines($exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
            $host=$matches[3];
            $date=$this->head_page("");
            $var1=$GLOBALS['wpdb']->get_results("SELECT value3 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='2' AND value1='s' AND value2='".$exp."';", ARRAY_N);
            $sql='';
            for($i=0;$i<count($var1);$i++){
                $sql.=($sql!=''?" OR":"(")." LOCATE('".$var1[$i][0]."',referer)!=0";
            };
            $sql.=")";
            $var=$GLOBALS['wpdb']->get_results("SELECT referer,count(referer) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND $sql AND code_stat=200 GROUP BY referer ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='".$var[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var[$i][0], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][1],0,',',' ')."</td></tr>";
            };
            if(count($var)==0){echo "<tr><td>".__("No URL","myStat")."</td></tr>";};
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Jumps from Search Engines","myStat"),'page_Jumps_from_Search_Engines');
        $var1=$GLOBALS['wpdb']->get_results("SELECT t1.value2,count(t2.referer) as sm,referer FROM ".$GLOBALS['wpdb']->prefix."myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t2.referer!='' AND t2.code_stat=200 AND t1.type='2' AND value1='s' AND LOCATE(value3,referer)!=0 GROUP BY t1.value2 ORDER BY sm DESC;", ARRAY_N);
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_page_Jumps_from_Search_Engines(id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 most popular addresses","myStat")."' title='".__("Show 20 most popular addresses","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> ".$var1[$i][0]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Jumps from Search Engines","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT referer) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer!='' AND code_stat=200;");
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other links","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page-$sum,0,',',' ')."</td></tr>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(referer) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer='' AND code_stat=200;");
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Direct jump (without a referrer)","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function  page_Jumps_from_Directories($exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
            $host=$matches[3];
            $date=$this->head_page("");
            $var1=$GLOBALS['wpdb']->get_results("SELECT value3 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='2' AND value1='c' AND value2='".$exp."';", ARRAY_N);
            $sql='';
            for($i=0;$i<count($var1);$i++){
                $sql.=($sql!=''?" OR":"(")." LOCATE('".$var1[$i][0]."',referer)!=0";
            };
            $sql.=")";
            $var=$GLOBALS['wpdb']->get_results("SELECT referer,count(referer) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND $sql AND code_stat=200 GROUP BY referer ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='".$var[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var[$i][0], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][1],0,',',' ')."</td></tr>";
            };
            if(count($var)==0){echo "<tr><td>".__("No URL","myStat")."</td></tr>";};
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Jumps from Directories","myStat"),'page_Jumps_from_Directories');
        $var1=$GLOBALS['wpdb']->get_results("SELECT t1.value2,count(t2.referer) as sm,referer FROM ".$GLOBALS['wpdb']->prefix."myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t2.referer!='' AND t2.code_stat=200 AND t1.type='2' AND value1='c' AND LOCATE(value3,referer)!=0 GROUP BY t1.value2 ORDER BY sm DESC;", ARRAY_N);
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_page_Jumps_from_Directories(id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 most popular addresses","myStat")."' title='".__("Show 20 most popular addresses","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> ".$var1[$i][0]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Jumps from Directories","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT referer) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer!='' AND code_stat=200;");
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other links","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page-$sum,0,',',' ')."</td></tr>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(referer) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer='' AND code_stat=200;");
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Direct jump (without a referrer)","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function  page_Jumps_from_Ratings($exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
            $host=$matches[3];
            $date=$this->head_page("");
            $var1=$GLOBALS['wpdb']->get_results("SELECT value3 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='2' AND value1='t' AND value2='".$exp."';", ARRAY_N);
            $sql='';
            for($i=0;$i<count($var1);$i++){
                $sql.=($sql!=''?" OR":"(")." LOCATE('".$var1[$i][0]."',referer)!=0";
            };
            $sql.=")";
            $var=$GLOBALS['wpdb']->get_results("SELECT referer,count(referer) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND $sql AND code_stat=200 GROUP BY referer ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='".$var[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var[$i][0], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][1],0,',',' ')."</td></tr>";
            };
            if(count($var)==0){echo "<tr><td>".__("No URL","myStat")."</td></tr>";};
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Jumps from Ratings","myStat"),'page_Jumps_from_Ratings');
        $var1=$GLOBALS['wpdb']->get_results("SELECT t1.value2,count(t2.referer) as sm,referer FROM ".$GLOBALS['wpdb']->prefix."myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t2.referer!='' AND t2.code_stat=200 AND t1.type='2' AND value1='t' AND LOCATE(value3,referer)!=0 GROUP BY t1.value2 ORDER BY sm DESC;", ARRAY_N);
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_page_Jumps_from_Ratings(id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 most popular addresses","myStat")."' title='".__("Show 20 most popular addresses","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> ".$var1[$i][0]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Jumps from Ratings","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT referer) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer!='' AND code_stat=200;");
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other links","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page-$sum,0,',',' ')."</td></tr>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(referer) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer='' AND code_stat=200;");
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Direct jump (without a referrer)","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function  page_Jumps_from_Popular_Sites($exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
            $host=$matches[3];
            $date=$this->head_page("");
            $var1=$GLOBALS['wpdb']->get_results("SELECT value3 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='2' AND value2='".$exp."';", ARRAY_N);
            $sql='';
            for($i=0;$i<count($var1);$i++){
                $sql.=($sql!=''?" OR":"(")." LOCATE('".$var1[$i][0]."',referer)!=0";
            };
            $sql.=")";
            $var=$GLOBALS['wpdb']->get_results("SELECT referer,count(referer) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND $sql AND code_stat=200 GROUP BY referer ORDER BY sm DESC LIMIT 0,20;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            for($i=0;$i<count($var);$i++){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'><a href='".$var[$i][0]."' target='_blank'>".$cmn->my_wordwrap($var[$i][0], 5, "<wbr/>")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][1],0,',',' ')."</td></tr>";
            };
            if(count($var)==0){echo "<tr><td>".__("No URL","myStat")."</td></tr>";};
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Jumps from Popular Sites","myStat"),'page_Jumps_from_Popular_Sites');
        $var1=$GLOBALS['wpdb']->get_results("SELECT t1.value2,count(t2.referer) as sm,referer FROM ".$GLOBALS['wpdb']->prefix."myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t2.referer!='' AND t2.code_stat=200 AND t1.type='2' AND LOCATE(value3,referer)!=0 GROUP BY t1.value2 ORDER BY sm DESC;", ARRAY_N);
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_page_Jumps_from_Popular_Sites(id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Show 20 most popular addresses","myStat")."' title='".__("Show 20 most popular addresses","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> ".$var1[$i][0]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Jumps from Popular Sites","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT referer) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer!='' AND code_stat=200;");
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other links","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page-$sum,0,',',' ')."</td></tr>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(referer) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer='' AND code_stat=200;");
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Direct jump (without a referrer)","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function page_Agents($page=1){
        $date=$this->head_page(__("Agents","myStat"),'page_Agents');
        $limit=30;$page--;
        $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' GROUP BY user_agent;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT user_agent,count(DISTINCT ip) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' GROUP BY user_agent ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Agents(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var2);$i++){
            $sum+=$var2[$i][0];
            if($max<$var2[$i][0]){$max=$var2[$i][0];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'> ".$cmn->my_wordwrap($var1[$i][0],5,'<wbr/>')."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Agents(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total jumps from pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique agents","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";

    }

###############################################################################################################################

    function page_Accept_Languages($page=1){
        $date=$this->head_page(__("Accept-Languages","myStat"),'page_Accept_Languages');
        $limit=30;$page--;
        $var2=$GLOBALS['wpdb']->get_results("SELECT count(DISTINCT ip) FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY lang;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT lang,count(DISTINCT ip) as sm,country FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY lang ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Accept_Languages(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var2);$i++){
            $sum+=$var2[$i][0];
            if($max<$var2[$i][0]){$max=$var2[$i][0];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        include_once("geoip/locale.php");
        $loc=new Locale();
        for($i=0;$i<count($var1);$i++){
            $tmp=$loc->get_locale(strtolower($var1[$i][0]));
            if(file_exists(WP_PLUGIN_DIR."/".dirname(dirname(plugin_basename(__FILE__)))."/images/flags/".strtolower($var1[$i][0]).".png")){
                $tmp1="<img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/flags/".strtolower($var1[$i][0]).".png' height='12px' width='18px' />";
            }else{$tmp1="";};
            $tmp=($tmp1!=''?$tmp1:"&nbsp;")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><b>".strtoupper($var1[$i][0])."</b>".($tmp["en_language"]!=''?" ".$tmp["en_language"]." (".$tmp["native_language"].")":"");
            if($var1[$i][0]==''){$all_page--;};
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".$j."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'> ".($var1[$i][0]!=''?$tmp:"&nbsp;</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><b>".__("Unknown","myStat")."</b>")."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Accept_Languages(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total jumps from pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique Accept-Languages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";

    }

###############################################################################################################################
    
    function page_Browsers($page=1,$max=0){
        $date=$this->head_page(__("Browsers","myStat"),'page_Browsers');
        
        $limit=30;$page--;
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT user_agent) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title!='' OR date_load!='0000-00-00 00:00:00');");

        $type=$GLOBALS['wpdb']->get_results("SELECT value1,value2 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='5' ORDER BY value3;", ARRAY_N);
        $limit=$all_page;
        $var1=$GLOBALS['wpdb']->get_results("SELECT user_agent,count(user_agent) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title!='' OR date_load!='0000-00-00 00:00:00') GROUP BY user_agent ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
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
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Browsers(\'[page_number]\','.$max.',\'myStat_load\');',$all_page,$tmp,$limit);
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
        $label[]=__("Other browsers","myStat");
        $data[]=($sum-$xz);
        echo "<br/><center>".$cmn->chart_html("p3",600,300,$data,$label)."</center>";
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
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Browsers(\'[page_number]\','.$max.',\'myStat_load\');',$all_page,$tmp,$limit);
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Browsers","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other browsers","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($no_detect,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function page_Screen_Resolution(){
        $date=$this->head_page(__("Screen Resolution","myStat"),'page_Screen_Resolution');

        $var1=$GLOBALS['wpdb']->get_results("SELECT screen,count(screen) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND screen!='' AND screen!='0x0' GROUP BY screen ORDER BY sm DESC;", ARRAY_N);
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
        $data[]=($sum-$xz);
        $label[]=__("Other resolution","myStat");

        include_once('common.class.php');
        $cmn=new myStat_common();
        echo "<br/><center>".$cmn->chart_html("p3",600,300,$data,$label)."</center>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'> ".$var1[$i][0]."<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(screen) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (screen='' OR screen='0x0') AND (date_load!='0000-00-00 00:00:00' or title!='');");
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Unknown","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($all_page,0,',',' ')."</b></td></tr>";
        echo "</table>";

    }

###############################################################################################################################

    function page_Colour_Depth(){
        $date=$this->head_page(__("Colour Depth","myStat"),'page_Colour_Depth');

        $var1=$GLOBALS['wpdb']->get_results("SELECT depth,count(depth) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND depth!='' GROUP BY depth ORDER BY sm DESC;", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'> ";
            echo $var1[$i][0];
            switch($var1[$i][0]){
                case 8: echo " <span style='color:silver;'>(".__("256 colors","myStat").")</span>";break;
                case 16: echo " <span style='color:silver;'>(".__("65 thousand colors","myStat").")</span>";break;
                case 24: echo " <span style='color:silver;'>(".__("16 mln. colors","myStat").")</span>";break;
                case 32: echo " <span style='color:silver;'>(".__("16 mln. colors","myStat").")</span>";break;
            };
            echo "<br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(depth) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND depth='' AND (date_load!='0000-00-00 00:00:00' or title!='');");
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Unknown","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($all_page,0,',',' ')."</b></td></tr>";
        echo "</table>";

    }

###############################################################################################################################

    function page_Operating_Systems($exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            $date=$this->head_page("");
            $x16=$GLOBALS['wpdb']->get_var("SELECT count(t2.user_agent) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t1.value1='".$exp."' AND t2.user_agent!='' AND t1.type='4' AND LOCATE(value2,user_agent)!=0 AND LOCATE('Win3.1',user_agent)!=0;");
            $x64=$GLOBALS['wpdb']->get_var("SELECT count(t2.user_agent) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t1.value1='".$exp."' AND t2.user_agent!='' AND t1.type='4' AND LOCATE(value2,user_agent)!=0 AND (LOCATE('WOW64',user_agent)!=0 OR LOCATE('Win64',user_agent)!=0 OR LOCATE('x64',user_agent)!=0);");
            $x32=$GLOBALS['wpdb']->get_var("SELECT count(t2.user_agent) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t1.value1='".$exp."' AND t2.user_agent!='' AND t1.type='4' AND LOCATE(value2,user_agent)!=0;");
            $x32=$x32-$x16-$x64;
            echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            if($x16>0){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("16 bit systems","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($x16,0,',',' ')."</td></tr>";
            };
            if($x32>0){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("32 bit systems","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($x32,0,',',' ')."</td></tr>";
            };
            if($x64>0){
                echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("64 bit systems","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($x64,0,',',' ')."</td></tr>";
            };
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Operating Systems","myStat"),'page_Operating_Systems');
        $var1=$GLOBALS['wpdb']->get_results("SELECT t1.value1,count(t2.user_agent) as sm,user_agent FROM ".$GLOBALS['wpdb']->prefix."myStat_data t1, ".$GLOBALS['wpdb']->prefix."myStat_main t2 WHERE  t2.date >= ('".$date[0]."') AND t2.date <= ('".$date[1]."') AND t2.user_agent!='' AND t1.type='4' AND LOCATE(value2,user_agent)!=0 GROUP BY t1.value1 ORDER BY sm DESC;", ARRAY_N);
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        for($i=0;$i<count($var1);$i++){
            echo "id[".$i."]='".mysql_escape_string($var1[$i][0])."';";
        };
        echo "x_page_Operating_Systems(id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $sum=0;$max=0;
        for($i=0;$i<count($var1);$i++){
            $sum+=$var1[$i][1];
            if($max<$var1[$i][1]){$max=$var1[$i][1];};
        };
        if($sum<1 and count($var1)>0){$sum=1;};
        for($i=0;$i<count($var1);$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("Type of systems","myStat")."' title='".__("Type of systems","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> ".$var1[$i][0]."<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$var1[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($var1[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$var1[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
        };
        echo "</table>";
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Operating Systems","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function page_Robots($page=1,$max=0,$exp='',$num=0){
        if($exp!=''){
            echo $num.'###';
            include_once('common.class.php');
            $cmn=new myStat_common();
            $date=$this->head_page("");
            $rob=$GLOBALS['wpdb']->get_results("SELECT value2 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE  value1='".$exp."' AND type='3';", ARRAY_N);
            $var1=$GLOBALS['wpdb']->get_results("SELECT user_agent,ip FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title='' OR date_load='0000-00-00 00:00:00') GROUP BY user_agent;", ARRAY_N);
            $wh='AND (';
            for($i=0;$i<count($var1);$i++){
                for($j=0;$j<count($rob);$j++){
                    $key='^'.str_replace(array('\\','.','?','*','^','$','[',']','|','(',')','+','{','}','%','/'),array('\\\\','\\.','.','.*','\\^','\\$','\\[','\\]','\\|','\\(','\\)','\\+','\\{','\\}','\\%','\\/'),$rob[$j][0]).'$';
                    if(preg_match('%'.$key.'%i',$var1[$i][0])){
                        $wh.=($wh!='AND ('?" OR ":"")."(user_agent='".$var1[$i][0]."')";
                        break;
                    };
                };
            };
            $wh.=")";
            $var1=$GLOBALS['wpdb']->get_results("SELECT min(date),max(date) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."')".$wh.";", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='background-color:#F2F2F2;border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            echo "<tr><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("The first visit","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".$var1[0][0]."</td></tr>";
            echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:10px;height:20px;border:solid #DDDDDD 1px;'>".__("The last visit","myStat")."</a></td><td style='padding:5px;font-size:11px;height:20px;border:solid #DDDDDD 1px;' align='center' nowrap>".$var1[0][1]."</td></tr>";
            echo "</table>";
            $var1=$GLOBALS['wpdb']->get_results("SELECT ip,count(*) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."')".$wh." GROUP BY ip ORDER BY sm DESC LIMIT 0,40;", ARRAY_N);
            echo "<table cellspacing='0' cellpadding='0' style='background-color:#F2F2F2;border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
            $j=0;
            for($i=0;$i<count($var1);$i=$i+4){
               echo "<tr".(floor($j/2)==$j/2?" style='background-color:#E6E6E6'":"").">";
               echo "<td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'".($var1[$i][0]!=''?">".long2ip($var1[$i][0])."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".$var1[$i][1]:" colspan='2'>&nbsp;")."</td>";
               echo "<td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'".($var1[$i+1][0]!=''?">".long2ip($var1[$i+1][0])."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".$var1[$i+1][1]:" colspan='2'>&nbsp;")."</td>";
               echo "<td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'".($var1[$i+2][0]!=''?">".long2ip($var1[$i+2][0])."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".$var1[$i+2][1]:" colspan='2'>&nbsp;")."</td>";
               echo "<td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'".($var1[$i+3][0]!=''?">".long2ip($var1[$i+3][0])."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center'>".$var1[$i+3][1]:" colspan='2'>&nbsp;")."</td>";
               echo "</tr>";    
               $j++;
            };
            echo "</table>";
            exit();
        };
        $date=$this->head_page(__("Robots","myStat"),'page_Robots');
        $limit=30;$page--;
        $all_page=$GLOBALS['wpdb']->get_var("SELECT count(DISTINCT user_agent) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title='' OR date_load='0000-00-00 00:00:00');");
        $type=$GLOBALS['wpdb']->get_results("SELECT value1,value2 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='3' ORDER BY value3;", ARRAY_N);
        $limit=$all_page;
        $var1=$GLOBALS['wpdb']->get_results("SELECT user_agent,count(user_agent) as sm FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND user_agent!='' AND code_stat=200 AND feed='no' AND (title='' OR date_load='0000-00-00 00:00:00') GROUP BY user_agent ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
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
        echo "<script>";
        echo "function myStat_expand(num){";
        echo "myStat_loading();";
        echo "var id=new Array();";
        $i=0;reset($new);
        while($fn = current($new)){
            echo "id[".$i."]='".mysql_escape_string(key($new))."';";
            $i++;
            next($new);
        };
        echo "x_page_Robots('".($page+1)."','".$max."',id[num],num,'myStat_expand_load');";
        echo "};";
        echo "function myStat_expand_load(data){a=data.split('###');document.getElementById('myStat_loading').style.display='none';el=document.getElementById(\"myStat_e\"+a[0]);el.style.display='';el.innerHTML=a[1];};";
        echo "</script>";
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Robots(\'[page_number]\','.$max.',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $i=0;reset($new);
        while($fn = current($new)){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><img style='cursor:pointer;' alt='".__("About this robot","myStat")."' title='".__("About this robot","myStat")."' src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ico_tree.gif' height='12px' width='14px' onclick='el=document.getElementById(\"myStat_e".$i."\");if(el.style.display!=\"\"){myStat_expand(".$i.");}else{el.style.display=\"none\";};'/> ";
            echo key($new);
            echo "<div style='display:none;' id='myStat_e".$i."'></div><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$fn/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($fn,0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$fn/$sum,2,',',' ')."%</sup></td></tr>";
            $i++;
            next($new);
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Robots(\'[page_number]\','.$max.',\'myStat_load\');',$all_page,$tmp,$limit);
        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Robots","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Other Robots","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($no_detect,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################

    function page_Search_Phrases($page=1){
        $date=$this->head_page(__("Search Phrases","myStat"),'page_Search_Phrases');
        $limit=30;$page--;
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
        $host=$matches[3];
        $type=$GLOBALS['wpdb']->get_results("SELECT value1,value2,value3,value4 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='1';", ARRAY_N);
        $var1=$GLOBALS['wpdb']->get_results("SELECT referer,count(referer) as sm,www,host,uri,DATE_FORMAT(date,'%d-%m-%Y') FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer!='' AND !(LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) GROUP BY referer ORDER BY sm DESC ,date DESC;", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
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
                        if($type[$j][3]!=''){
                            $tt[$k][3]=iconv($type[$j][3],'UTF-8',$tt[$k][3]);
                        };
                        $tt[$k][4]="http://".($var1[$i][2]=='yes'?"www.":"").$var1[$i][3].$var1[$i][4];
                        $tt[$k][5]=$var1[$i][5];
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
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Search_Phrases(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
        $j=$page*$limit+1;
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $end=$limit*$page+$limit;if($end>$all_page){$end=$all_page;};
        for($i=$limit*$page;$i<$end;$i++){
            echo "<tr".(floor($i/2)==$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".($j)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'><sup>[ ".$tt[$i][5]." ]</sup> <b>".$tt[$i][0].":</b> <a href=".$tt[$i][2]." target='_blank'>".$tt[$i][3]."</a><br/>&nbsp; <sup><i><b>".__("Page Found","myStat").":</b></i> <a href=".$tt[$i][4]." target='_blank'>".$cmn->my_wordwrap($tt[$i][4],5,'<wbr/>')."</a></sup><br/><img src='".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/ind.png' height='11px' width='".ceil(100*$tt[$i][1]/$max)."%'/></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap valign='top'><b>".number_format($tt[$i][1],0,',',' ')."</b><br/><sup style='color:silver;'>".number_format(100*$tt[$i][1]/$sum,2,',',' ')."%</sup></td></tr>";
            $j++;
        };
        echo "</table>";
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Search_Phrases(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
    }

###############################################################################################################################

    function page_Pageviews_per_User(){
        $date=$this->head_page(__("Pageviews per User","myStat"),'page_Pageviews_per_User');
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $var1=$GLOBALS['wpdb']->get_results("SELECT sum(count) as st, ip FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (date_load!='0000-00-00 00:00:00' or title!='')  GROUP BY ip ORDER by st;", ARRAY_N);
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

###############################################################################################################################

    function page_Loading_Speed(){
        $date=$this->head_page(__("Loading Speed","myStat"),'page_Loading_Speed');
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $var1=$GLOBALS['wpdb']->get_results("SELECT date_load-date as st FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (date_load!='0000-00-00 00:00:00' or title!='') ORDER by st;", ARRAY_N);
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

###############################################################################################################################

    function page_Domain_Names(){
        $date=$this->head_page(__("Domain Names","myStat"),'page_Domain_Names');
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        $var1=$GLOBALS['wpdb']->get_results("SELECT host,count(www) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND www='no' GROUP BY host;", ARRAY_N);
        $var2=$GLOBALS['wpdb']->get_results("SELECT host,count(www) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND www='yes' GROUP BY host;", ARRAY_N);
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

###############################################################################################################################

    function page_Search_Phrases_by_Date($page=1){
        $date=$this->head_page(__("Search Phrases by Date","myStat"),'page_Search_Phrases_by_Date');
        $limit=30;$page--;
        preg_match("/(^http:\/\/)?(www\.)?.*?([^\/]+)/i",$GLOBALS['_SERVER']['HTTP_HOST'], $matches);
        $host=$matches[3];
        $type=$GLOBALS['wpdb']->get_results("SELECT value1,value2,value3,value4 FROM ".$GLOBALS['wpdb']->prefix."myStat_data WHERE type='1';", ARRAY_N);
        $var1=$GLOBALS['wpdb']->get_results("SELECT referer,count,www,host,uri,DATE_FORMAT(date,'%d-%m-%Y %H-%i'),DATE_FORMAT(date,'%d-%m-%Y') FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND referer!='' AND !(LOCATE('".$host."',referer)>7 AND LOCATE('".$host."',referer)<12) ORDER BY date DESC;", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
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
                        if($type[$j][3]!=''){
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
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Search_Phrases_by_Date(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
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
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Search_Phrases_by_Date(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
    }

###############################################################################################################################
    
    function page_JavaScript(){
        $date=$this->head_page(__("JavaScript","myStat"),'page_JavaScript');
        $var1=$GLOBALS['wpdb']->get_results("SELECT js,count(DISTINCT ip) FROM ".$GLOBALS['wpdb']->prefix."myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND (date_load!='0000-00-00 00:00:00' or title!='') GROUP BY js ORDER BY js;", ARRAY_N);
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

###############################################################################################################################
    
    function page_Configuration($SD='',$SPS=''){
        if($SD!=''){
            update_option("myStat_saveday",$SD);
            if($SPS=='true'){update_option("myStat_show_post_stat",1);}else{update_option("myStat_show_post_stat",0);};
        };
        echo "&nbsp; <b>".__("Configuration","myStat")."</b><br/>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr style='background-color:#E6E6E6'><td colspan=2 style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><b>".__("Main settings","myStat")."</b></td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'>".__("It sets period of complete statistics storage per days. The longer the period, the more space is required for the database. The database size influences the system performance, it slows down with larger database. Database size can be monitored in the report \"Database size\".","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>";
        echo "<select id=myStat_SD name=savedays>";
        echo "<option ".(get_option("myStat_saveday")==30?"selected":"")." value=30>30 ".__("days","myStat")."</option>";
        echo "<option ".(get_option("myStat_saveday")==60?"selected":"")." value=60>60 ".__("days","myStat")."</option>";
        echo "<option ".(get_option("myStat_saveday")==90?"selected":"")." value=90>90 ".__("days","myStat")."</option>";
        echo "<option ".(get_option("myStat_saveday")==120?"selected":"")." value=120>120 ".__("days","myStat")."</option>";
        echo "<option ".(get_option("myStat_saveday")==180?"selected":"")." value=180>180 ".__("days","myStat")."</option>";
        echo "<option ".(get_option("myStat_saveday")==240?"selected":"")." value=240>240 ".__("days","myStat")."</option>";
        echo "<option ".(get_option("myStat_saveday")==365?"selected":"")." value=365>".__("1 year","myStat")."</option>";
        echo "<option ".(get_option("myStat_saveday")==545?"selected":"")." value=545>".__("1,5 years","myStat")."</option>";
        echo "<option ".(get_option("myStat_saveday")==730?"selected":"")." value=730>".__("2 years","myStat")."</option>";
        echo "</select>";
        echo "</td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'>".__("Show unique visitors to post","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>";
        echo "<input id=myStat_SPS type=checkbox name=show_post_stat ".(get_option("myStat_show_post_stat")==1?"checked ":"")."/>";
        echo "</td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td colspan=2 style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><input class='button-primary' onclick=\"myStat_loading();x_page_Configuration(document.getElementById('myStat_SD').value,document.getElementById('myStat_SPS').checked,'myStat_load');\" type=button value='".__("Save settings","myStat")."' /></td></tr>";


        echo "</table>";

    }

###############################################################################################################################

    function page_Popular_Posts($page=1){
        $date=$this->head_page(__("Popular Posts","myStat"),'page_Popular_Posts');
        $limit=30;$page--;
        $var2=$GLOBALS['wpdb']->get_results("SELECT sum(count) as sm FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND post_id!=0 GROUP BY post_id;", ARRAY_N);
        $all_page=count($var2);
        $var1=$GLOBALS['wpdb']->get_results("SELECT host,uri,title,sum(count) as sm,post_id FROM `".$GLOBALS['wpdb']->prefix."myStat_main` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND code_stat=200 AND post_id!=0 GROUP BY post_id ORDER BY sm DESC LIMIT ".($page*$limit).", ".$limit.";", ARRAY_N);
        include_once('common.class.php');
        $cmn=new myStat_common();
        $tmp=$page+1;
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Popular_Posts(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);
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
        echo $cmn->getPaginationLinks('javascript:myStat_loading();x_page_Popular_Posts(\'[page_number]\',\'myStat_load\');',$all_page,$tmp,$limit);

        echo "<br/><b>&nbsp; ".__("Total:","myStat")."</b><br/>";
        echo "<table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr".(floor(($i+1)/2)==($i+1)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total requests for pages","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($sum,0,',',' ')."</td></tr>";
        echo "<tr".(floor(($i+2)/2)==($i+2)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Total unique pages addresses","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap>".number_format($all_page,0,',',' ')."</td></tr>";
        echo "</table>";
    }

###############################################################################################################################
};
?>