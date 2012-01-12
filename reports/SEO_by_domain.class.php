<?php

class SEO_by_domain{

    static function getTitle(){
        return __("SEO by domain","myStat");
    }

    static function getMenuItemName(){
        return array(__('SEO by domain','myStat'),4);
    }

    static function getMenuTreeName(){
        return array(__('SEO','myStat'),6);
    }

    function init($date){
        global $cmn;
        if($cmn->getParam("myStat_debug")==1){$cmn->setDebug('LOADING (report '.$this->getTitle().')');};
        echo "<br/><center>";
        _e("Choose domain","myStat");
        echo ": <select>";
        $var1=$cmn->getSQL("SELECT host FROM %%PREFIX%%myStat_main WHERE date >= ('".$date[0]."') AND date <= ('".$date[1]."') GROUP BY host;");
        $max=0;$sum=0;$count=0;
        $j=1;
        for($i=0;$i<count($var1);$i++){
            echo "<option value='".$var1[$i][0]."'> [".$j."] ".$var1[$i][0]." &nbsp;</option>";
            $j++;
        };
        echo "</select></center>";
        echo "<br/><table cellspacing='0' cellpadding='0' style='border:0;' width='100%'><tr><td>";

        echo "<br/><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width=50%>";
        $google = new GooglePR;
        $googlepr = $google->getPagerank ("http://".$var1[0][0].'/');
        echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'>".__("Google PR","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'><b>".$googlepr."</b></td></tr>";

        $yandex = new YandexCY;
        $yandexcy = $yandex->getCY ("http://".$var1[0][0].'/');
        echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;'>".__("Yandex CY","myStat")."</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'><b>".$yandexcy."</b></td></tr>";

        echo "</table>";
        echo "</td></tr></table>";
    }
};

class YandexCY
{
  function getCY ($url)
  {
    $response = file ("http://bar-navig.yandex.ru/u?ver=2&url=".urlencode($url)."&show=1&post=0");
 
    for ($i=0; $i<sizeof($response); $i++)
      {
        $num_found = preg_match('/<tcy rang="(\d+)" value="(\d+)"\/>/', $response[$i], $matches);
        if ($num_found > 0) {
          return $matches[1].'/'.$matches[2];
        }
      }
  }
};

class GooglePR {
 
    function zeroFill($a, $b) {
        $z = hexdec(80000000);
        if ($z & $a) {
            $a = ($a>>1);
            $a &= (~$z);
            $a |= 0x40000000;
            $a = ($a>>($b-1));
        } else {
            $a = ($a>>$b);
        }
        return $a;
    }

    function GPR_toHex8($intega){
        $Ziffer = "0123456789abcdef";
        return $Ziffer[($intega%256)/16].$Ziffer[$intega%16];
    }

    function GPR_hexEncodeU32($num) {
        $result = $this->GPR_toHex8($this->zeroFill($num,24));
        $result .= $this->GPR_toHex8($this->zeroFill($num,16) & 255);
        $result .= $this->GPR_toHex8($this->zeroFill($num,8) & 255);
        return $result . $this->GPR_toHex8($num & 255);
    }


    function GPR_awesomeHash($value) {
        $GPR_HASH_SEED = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
        $kindOfThingAnIdiotWouldHaveOnHisLuggage = 16909125;
        for($i = 0; $i < strlen($value); $i++ ) {
            $kindOfThingAnIdiotWouldHaveOnHisLuggage ^= ord(substr($GPR_HASH_SEED, $i % strlen($GPR_HASH_SEED),1)) ^ ord(substr($value, $i,1));
            $kindOfThingAnIdiotWouldHaveOnHisLuggage = $this->zeroFill($kindOfThingAnIdiotWouldHaveOnHisLuggage,23) | $kindOfThingAnIdiotWouldHaveOnHisLuggage << 9;
        }
        return '8'.$this->GPR_hexEncodeU32($kindOfThingAnIdiotWouldHaveOnHisLuggage);
    }

    function getPagerank($url) {

        $file = $this->getPRurl($url);
        $data = file($file);
        $rankarray = explode (':', $data[0]);
        $rank = $rankarray[2];
        if (!$rank) $rank=0;
        return $rank;
    }

    function getPRurl($url) {

        $ch = $this->GPR_awesomeHash($url);
        $prurl = "http://toolbarqueries.google.com/search?client=navclient-auto&features=Rank&ch=$ch&q=info:$url";
        return $prurl;
    }
}
?>