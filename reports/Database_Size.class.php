<?php

class Database_Size{

    static function getTitle(){
        return __("Database Size","myStat");
    }

    static function getMenuItemName(){
        return array(__('Database Size','myStat'),1);
    }

    static function getMenuTreeName(){
        return array(__('Configuration','myStat'),7);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        $var=$cmn->getSQL("SELECT date,size,DATE_FORMAT(date,'%d') FROM `%%PREFIX%%myStat_dbsize` WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') ORDER BY date DESC;");
        $diff=count($var)>30?floor(count($var)/30):1;
        $data=$data1=$label=array();
        $maxG=number_format(90,1,'.','');
        $minG=number_format(10,1,'.','');
        for($i=count($var)-1;$i>=0;$i=$i-$diff){
            $data1[]=$var[$i][1];
            $label[]=$var[$i][2];
        };
        $max=max($data1);
        $min=min($data1);
        $avg=count($data1)>0?floor(array_sum($data1)/count($data1)):1;
        $avgG=number_format((floor(($avg-$min)*(($maxG*10)-($minG*10))/(($max-$min)>0?($max-$min):1))+($minG*10))/10,1,'.','');
        for($i=0;$i<count($data1);$i++){
            $data[]=number_format((floor(($data1[$i]-$min)*(($maxG*10)-($minG*10))/(($max-$min)>0?($max-$min):1))+($minG*10))/10,1,'.','');
        };
        if(count($var)>0){
            echo "<br/><center>";
            echo "<img width=600px height=300px src=\"http://chart.apis.google.com/chart?cht=lc&chco=00FF00&chs=600x300&chf=bg,s,F2F2F2&chg=10,20&chd=t:".implode(',', $data)."&chxl=0:|".implode('|', (array_map("urlencode", $label)))."|1:|".$min."|".$avg."|".$max."&chxt=x,y&chxp=1,".$minG.",".$avgG.",".$maxG."\"/>";
            echo "</center>";
        };
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>&nbsp;</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align=center><b>".__("Date","myStat")."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center' nowrap><b>".__("Database Size","myStat")."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center' nowrap><b>".__("Changes from the previous day","myStat")."</b></td></tr>";
        for($i=0;$i<count($var);$i++){
            $nn=($i!=count($var)-1?$var[$i][1]-$var[$i+1][1]:$var[$i][1]);
            echo "<tr".(floor($i/2)!=$i/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'><b>".($i+1)."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align=center><b>".$var[$i][0]."</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center' nowrap>".number_format($var[$i][1],0,',',' ')."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='center' nowrap>".($nn<0?"<a style='color:red'>":"").number_format($nn,0,',',' ').($nn<0?"</a>":"")."</td></tr>";
        };
        echo "</table>";
    }
};
?>