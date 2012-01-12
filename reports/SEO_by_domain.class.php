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

  var $googlehost='www.google.com';
  var $googleua='Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.6) Gecko/20060728 Firefox/1.5';

  private function StrToNum($Str, $Check, $Magic) {
    $Int32Unit = 4294967296; // 2^32
    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++) {
      $Check *= $Magic;
      if ($Check >= $Int32Unit) {
        $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
        $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
      }
      $Check += ord($Str{$i});
    }
    return $Check;
  }

  private function HashURL($String) {
    $Check1 = $this->StrToNum($String, 0x1505, 0x21);
    $Check2 = $this->StrToNum($String, 0, 0x1003F);

    $Check1 >>= 2;
    $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
    $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
    $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);

    $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
    $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );

    return ($T1 | $T2);
  }

  private function CheckHash($Hashnum) {
    $CheckByte = 0;
    $Flag = 0;

    $HashStr = sprintf('%u', $Hashnum) ;
    $length = strlen($HashStr);

    for ($i = $length - 1; $i >= 0; $i --) {
      $Re = $HashStr{$i};
      if (1 === ($Flag % 2)) {
        $Re += $Re;
        $Re = (int)($Re / 10) + ($Re % 10);
      }
      $CheckByte += $Re;
      $Flag ++;
    }
    $CheckByte %= 10;
    if (0 !== $CheckByte) {
      $CheckByte = 10 - $CheckByte;
      if (1 === ($Flag % 2) ) {
        if (1 === ($CheckByte % 2)) {
          $CheckByte += 9;
        }
        $CheckByte >>= 1;
      }
    }
    return '7'.$CheckByte.$HashStr;
  }

  private function getch($url) {
    return $this->CheckHash($this->HashURL($url));
  }

  private function getpr($url) {
    $ch = $this->getch($url);
    $fp = fsockopen($this->googlehost, 80, $errno, $errstr, 10);
    if ($fp) {
      $out = "GET /search?client=navclient-auto&ch=$ch&features=Rank&q=info:$url HTTP/1.1\r\n";
      $out .= "User-Agent: $this->googleua\r\n";
      $out .= "Host: $this->googlehost\r\n";
      $out .= "Connection: Close\r\n\r\n";

      fwrite($fp, $out);

      while (!feof($fp)) {
        $data = fgets($fp, 128);
        $pos = strpos($data, "Rank_");
        if($pos === false){} else{
          $pr=substr($data, $pos + 9);
          $pr=trim($pr);
          $pr=str_replace("\n",'',$pr);
          fclose($fp);
          return $pr;
        }
      }
      fclose($fp);
    }
  }

  public function getPagerank($url) {
    if(!preg_match('/^(http:\/\/)?([^\/]+)/i', $url)) { $url='http://'.$url; }
    $pr=$this->getpr($url);
    return (int)$pr;
  }
};

?>