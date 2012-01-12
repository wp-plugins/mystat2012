<?php

class Colour_Depth{

    static function getTitle(){
        return __("Colour Depth","myStat");
    }

    static function getMenuItemName(){
        return array(__('Colour Depth','myStat'),5);
    }

    static function getMenuTreeName(){
        return array(__('System','myStat'),5);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        $var1=$cmn->getSQL("SELECT depth,count(depth) as sm FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND depth!='' GROUP BY depth ORDER BY sm DESC;");
        $all_page=$cmn->getSQLONE("SELECT count(depth) as sm FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') AND depth='' AND (date_load!='0000-00-00 00:00:00' or title!='');");
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
        $data[]=$all_page;
        $label[]=__("Unknown","myStat");
        if(count($var1)>4){
            $data[]=($sum-$xz);
            $label[]=__("Other colour depth","myStat");
        };
        if(count($var1)>0){echo "<br/><center>".$cmn->chart_html("p3",600,200,$data,$label)."</center>";};
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='100%'>";
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
        echo "<tr".(floor(($i)/2)==($i)/2?" style='background-color:#E6E6E6'":"")."><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' width='100%'>".__("Unknown","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' align='right' nowrap><b>".number_format($all_page,0,',',' ')."</b></td></tr>";
        echo "</table>";
    }
};
?>