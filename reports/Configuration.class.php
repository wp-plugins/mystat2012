<?php

class Configuration{
    
    static function getTitle(){
        return __("Configuration","myStat");
    }

    static function getMenuItemName(){
        return array(__('Configuration','myStat'),2);
    }

    static function getMenuTreeName(){
        return array(__('Configuration','myStat'),7);
    }

    function init($date,$SD='',$SPS='',$debug=''){
        global $cmn;
        if($SD!=''){
            if('777'!=substr(sprintf('%o', fileperms(dirname(__FILE__).'/../data/')), -3)){
              echo "<div class='updated fade'><center><b>".__("Changes dir mode to 0777 for '/data/' dir","myStat")."</b></center></div><br/>";
            }else{
              if($debug=='true'){$cmn->setParam("myStat_debug",1);};
            };
            if($debug!='true'){$cmn->setParam("myStat_debug",0);};
            $cmn->setParam("myStat_saveday",$SD);
            if($SPS=='true'){$cmn->setParam("myStat_show_post_stat",1);}else{$cmn->setParam("myStat_show_post_stat",0);};
            if($cmn->getParam("myStat_debug")==1 and $debug!='true'){unlink(dirname(__FILE__).'/../data/debug.log');};
            if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('SAVE OPTIONS (report '.$this->getTitle().')');};
        };
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
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
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'>".__("Show unique visitors to post","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>";
        echo "<input id=myStat_SPS type=checkbox name=show_post_stat ".(get_option("myStat_show_post_stat")==1?"checked ":"")."/>";
        echo "</td></tr>";
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'>".__("Log debug information","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>";
        echo "<input id=myStat_debug type=checkbox name=mystat_debug ".(get_option("myStat_debug")==1?"checked ":"")."/>";
        echo "</td></tr>";
        echo "<tr style='background-color:#E6E6E6'><td colspan=2 style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;' valign='top'><input class='button-primary' onclick=\"myStat_loading();x_Configuration(document.getElementById('myStat_SD').value,document.getElementById('myStat_SPS').checked,document.getElementById('myStat_debug').checked,'myStat_load');\" type=button value='".__("Save settings","myStat")."' /></td></tr>";


        echo "</table>";
        return "NODATE";
    }
};

?>