<?php
class myStat_common{

    private $connect = null;
    private $prefix = '';

    function unicodeUrlDecode($url, $encoding = ""){
        if ($encoding == ''){
            if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])){
                preg_match('/^\s*([-\w]+?)([,;\s]|$)/', $_SERVER['HTTP_ACCEPT_CHARSET'], $a);
                $encoding = strtoupper($a[1]);
            }else{
                $encoding = 'UTF-8';
            };
        };
        preg_match_all('/%u([[:xdigit:]]{4})/', $url, $a);
        foreach ($a[1] as $unicode){
            $num = hexdec($unicode);
            $str = '';
            if ($num < 0x80)
                $str = chr($num);
            else if ($num < 0x800)
                $str = chr(0xc0 | (($num & 0x7c0) >> 6)) . chr(0x80 | ($num & 0x3f));
            else if ($num < 0x10000)
                $str = chr(0xe0 | (($num & 0xf000) >> 12)) . chr(0x80 | (($num & 0xfc0) >> 6)) . chr(0x80 | ($num & 0x3f));
            else
                $str = chr(0xf0 | (($num & 0x1c0000) >> 18)) . chr(0x80 | (($num & 0x3f000) >> 12)) . chr(0x80 | (($num & 0xfc0) >> 6)) . chr(0x80 | ($num & 0x3f));

            $str = iconv("UTF-8", "$encoding//IGNORE", $str);
            $url = str_replace('%u'.$unicode, $str, $url);
        };
        return urldecode($url);
    }

    function getPaginationLinks($url, $count_elements, $current_page, $count_element_on_page = 10){
      $out = '';

      $url = str_replace('%', '%%', $url);
      $url = str_replace('[page_number]', '%d', $url);

      $count_view_pages = 11;
      $middle_position = 6;
      if($count_element_on_page==0){$count_element_on_page=1;};
      $count_total_pages = ceil($count_elements/$count_element_on_page);
      if($count_total_pages==0){$count_total_pages = 1;};

      if($count_total_pages == 1){
        return '';
      }

      $middle_page = $current_page;
      $first_view_page  = $middle_page - ($middle_position - 1);
      if($first_view_page<1 ||($count_total_pages <= $count_view_pages))
      {
        $first_view_page = 1;
      };

      $prev_page = $current_page - 1;
      if($first_view_page > 1)
        $home_page = 1;
      else
        $home_page = 0;

      $last_view_page = $first_view_page + $count_view_pages - 1;
      if($last_view_page > $count_total_pages){$last_view_page = $count_total_pages;};

      $next_page = $current_page + 1;
      if($next_page > $count_total_pages){$next_page = 0;};

      if($count_total_pages > $last_view_page)
        $end_page = $count_total_pages;
      else
        $end_page = 0;

      if(($last_view_page + 1 - $first_view_page) < $count_view_pages && ($last_view_page - $count_view_pages + 1) >0)
      {
        $first_view_page = $last_view_page - $count_view_pages + 1;
      }

      $out.='<div class="tablenav"><div class="tablenav-pages" style="text-align:center;width:100%;">';

      if($home_page > 0) $out .= sprintf('<a href="'.$url.'"><img style="vertical-align:middle;" src="'.WP_PLUGIN_URL.'/'.dirname(dirname(plugin_basename(__FILE__))).'/images/prev.png" alt="" /></a>', $home_page);
      if($prev_page > 0) $out .= sprintf(' <a class="next page-numbers" href="'.$url.'">&laquo;</a>', $prev_page);

      for($i=$first_view_page; $i<=$last_view_page; $i++)
      {
        if($current_page == $i) $out .= sprintf('<span class="page-numbers current">%d</span>',$i);
        else $out .= sprintf(' <a class="page-numbers" href="'.$url.'">%d</a>', $i, $i);
      }

      if($next_page > 0) $out .= sprintf(' <a class="next page-numbers" href="'.$url.'">&raquo;</a>', $next_page);
      if($end_page > 0) $out .= sprintf(' <a href="'.$url.'"><img style="vertical-align:middle;" src="'.WP_PLUGIN_URL.'/'.dirname(dirname(plugin_basename(__FILE__))).'/images/next.png"/></a>', $end_page);

      $out.='</div></div>';

      return $out;
    }

    function my_wordwrap($text,$count,$delim){
        $arr = str_split($text, $count);
        for($i=0;$i<count($arr);$i++){
            $arr[$i]=$arr[$i].$delim;
        };
        return join('',$arr);
    }

    function whois($ip,$url="whois.arin.net"){
        $sock = fsockopen($url, 43, $errno, $errstr);
        if (!$sock) exit("$errno($errstr)");
        else{
            fputs ($sock, $ip."\r\n");
            $text = "";
            while (!feof($sock)){
                $text .= fgets ($sock, 128)."<br>";
            };
            fclose ($sock);
            $pattern = "|ReferralServer: whois://([^\n<:]+)|i";
            preg_match($pattern, $text, $out);
            if(!empty($out[1])) return $this->whois($ip,$out[1]);
            else return $text;
        };
    }

    function calendar(){
        echo "<style>";
        echo "img.tcalIcon{cursor: pointer;margin-left: 1px;vertical-align: middle;}div#tcal{position: absolute;visibility: hidden;z-index: 100;width: 158px;padding: 2px 0 0 0;}div#tcal table{width: 100%;border: 1px solid silver;border-collapse: collapse;background-color: white;}div#tcal table.ctrl{border-bottom: 0;}div#tcal table.ctrl td{width: 15px;height: 20px;}div#tcal table.ctrl th{background-color: white;color: black;border: 0;}div#tcal th{border: 1px solid silver;border-collapse: collapse;text-align: center;padding: 3px 0;font-family: tahoma, verdana, arial;font-size: 10px;background-color: gray;color: white;}div#tcal td{border: 0;border-collapse: collapse;text-align: center;padding: 2px 0;font-family: tahoma, verdana, arial;font-size: 11px;width: 22px;cursor: pointer;}div#tcal td.othermonth{color: silver;}div#tcal td.weekend{background-color: #ACD6F5;}div#tcal td.today{border: 1px solid red;}div#tcal td.selected{background-color: #FFB3BE;}iframe#tcalIF{position: absolute;visibility: hidden;z-index: 98;border: 0;}div#tcalShade{position: absolute;visibility: hidden;z-index: 99;}div#tcalShade table{border: 0;border-collapse: collapse;width: 100%;}div#tcalShade table td{border: 0;border-collapse: collapse;padding: 0;}";
        echo "</style>";
        echo "<script>";
        echo "var A_TCALDEF = {'months' : ['".__("January","myStat")."', '".__("February","myStat")."', '".__("March","myStat")."', '".__("April","myStat")."', '".__("May","myStat")."', '".__("June","myStat")."', '".__("July","myStat")."', '".__("August","myStat")."', '".__("September","myStat")."', '".__("October","myStat")."', '".__("November","myStat")."', '".__("December","myStat")."'],'weekdays' : ['".__("Su","myStat")."', '".__("Mo","myStat")."', '".__("Tu","myStat")."', '".__("We","myStat")."', '".__("Th","myStat")."', '".__("Fr","myStat")."', '".__("Sa","myStat")."'],'yearscroll': true,'weekstart': 1,'centyear'  : 70,'imgpath' : '".WP_PLUGIN_URL."/".dirname(dirname(plugin_basename(__FILE__)))."/images/'};";
        echo "function f_tcalParseDate(s_date){var re_date = /^\\s*(\\d{2,4})-(\\d{1,2})-(\\d{1,2}) (\\d{2}):(\\d{2}):(\\d{2})\\s*$/;if(!re_date.exec(s_date))return alert (\"Invalid date: '\" + s_date + \"'.\\nAccepted format is yyyy-mm-dd hh:mm:ss.\");var n_day = Number(RegExp.$3),n_month = Number(RegExp.$2),n_year = Number(RegExp.$1);n_time = RegExp.$4+':'+RegExp.$5+':'+RegExp.$6;if (n_year < 100)n_year += (n_year < this.a_tpl.centyear ? 2000 : 1900);if (n_month < 1 || n_month > 12)return alert (\"Invalid month value: '\" + n_month + \"'.\\nAllowed range is 01-12.\");var d_numdays = new Date(n_year, n_month, 0);if (n_day > d_numdays.getDate())return alert(\"Invalid day of month value: '\" + n_day + \"'.\\nAllowed range for selected month is 01 - \" + d_numdays.getDate() + \".\");return new Date (n_year, n_month - 1, n_day);};function f_tcalGenerDate (d_date) {return (d_date.getFullYear()+\"-\"+(d_date.getMonth() < 9 ? '0' : '') + (d_date.getMonth() + 1) + \"-\"+ (d_date.getDate() < 10 ? '0' : '') + d_date.getDate() + \" \"+ n_time);};function tcal (a_cfg, a_tpl){if (!a_tpl)a_tpl = A_TCALDEF;if (!window.A_TCALS)window.A_TCALS = [];if (!window.A_TCALSIDX)window.A_TCALSIDX = [];this.s_id = a_cfg.id ? a_cfg.id : A_TCALS.length;window.A_TCALS[this.s_id] = this;window.A_TCALSIDX[window.A_TCALSIDX.length] = this;this.f_show = f_tcalShow;this.f_hide = f_tcalHide;this.f_toggle = f_tcalToggle;this.f_update = f_tcalUpdate;this.f_relDate = f_tcalRelDate;this.f_parseDate = f_tcalParseDate;this.f_generDate = f_tcalGenerDate;this.s_iconId = 'tcalico_' + this.s_id;this.e_icon = f_getElement(this.s_iconId);if (!this.e_icon) {document.write('<img src=\"' + a_tpl.imgpath + 'cal.gif\" id=\"' + this.s_iconId + '\" onclick=\"A_TCALS[\\'' + this.s_id + '\\'].f_toggle()\" class=\"tcalIcon\" alt=\"".__("Open Calendar","myStat")."\" />');this.e_icon = f_getElement(this.s_iconId);};this.a_cfg = a_cfg;this.a_tpl = a_tpl;};function f_tcalShow (d_date){if (!this.a_cfg.controlname)throw(\"TC: control name is not specified\");if (this.a_cfg.formname) {var e_form = document.forms[this.a_cfg.formname];if (!e_form)throw(\"TC: form '\" + this.a_cfg.formname + \"' can not be found\");this.e_input = e_form.elements[this.a_cfg.controlname];}else this.e_input = f_getElement(this.a_cfg.controlname);if (!this.e_input || !this.e_input.tagName || this.e_input.tagName != 'INPUT')throw(\"TC: element '\" + this.a_cfg.controlname + \"' does not exist in \"+ (this.a_cfg.formname ? \"form '\" + this.a_cfg.controlname + \"'\" : 'this document'));this.e_div = f_getElement('tcal');if (!this.e_div) {this.e_div = document.createElement(\"DIV\");this.e_div.id = 'tcal';document.body.appendChild(this.e_div);};this.e_shade = f_getElement('tcalShade');if (!this.e_shade) {this.e_shade = document.createElement(\"DIV\");this.e_shade.id = 'tcalShade';document.body.appendChild(this.e_shade);};this.e_iframe =  f_getElement('tcalIF');if (b_ieFix && !this.e_iframe) {this.e_iframe = document.createElement(\"IFRAME\");this.e_iframe.style.filter = 'alpha(opacity=0)';this.e_iframe.id = 'tcalIF';this.e_iframe.src=this.a_tpl.imgpath+'pixel.gif';document.body.appendChild(this.e_iframe);};f_tcalHideAll();this.e_icon=f_getElement(this.s_iconId);if(!this.f_update())return;this.e_div.style.visibility='visible';this.e_shade.style.visibility='visible';if(this.e_iframe)this.e_iframe.style.visibility='visible';this.e_icon.src=this.a_tpl.imgpath+'no_cal.gif';this.e_icon.title='".__("Close Calendar","myStat")."';this.b_visible=true;};function f_tcalHide (n_date) {if (n_date)this.e_input.value = this.f_generDate(new Date(n_date));if (!this.b_visible)return;if (this.e_iframe)this.e_iframe.style.visibility = 'hidden';if (this.e_shade)this.e_shade.style.visibility = 'hidden';this.e_div.style.visibility = 'hidden';this.e_icon = f_getElement(this.s_iconId);this.e_icon.src = this.a_tpl.imgpath + 'cal.gif';this.e_icon.title = '".__("Open Calendar","myStat")."';this.b_visible = false;};function f_tcalToggle () {return this.b_visible ? this.f_hide() : this.f_show();};function f_tcalUpdate (d_date) {var d_client = new Date();d_client.setHours(0);d_client.setMinutes(0);d_client.setSeconds(0);d_client.setMilliseconds(0);var d_today = this.a_cfg.today ? this.f_parseDate(this.a_cfg.today) : d_client;var d_selected = this.e_input.value == ''? (this.a_cfg.selected ? this.f_parseDate(this.a_cfg.selected) : d_today): this.f_parseDate(this.e_input.value);if (!d_date)d_date = d_selected;else if (typeof(d_date) == 'number')d_date = new Date(d_date);else if (typeof(d_date) == 'string')this.f_parseDate(d_date);if (!d_date) return false;var d_firstday = new Date(d_date);d_firstday.setDate(1);d_firstday.setDate(1 - (7 + d_firstday.getDay() - this.a_tpl.weekstart) % 7);var a_class, s_html = '<table class=\"ctrl\"><tbody><tr>'+ (this.a_tpl.yearscroll ? '<td' + this.f_relDate(d_date, -1, 'y') + ' title=\"".__("Previous Year","myStat")."\"><img src=\"' + this.a_tpl.imgpath + 'prev_year.gif\" /></td>' : '')+ '<td' + this.f_relDate(d_date, -1) + ' title=\"".__("Previous Month","myStat")."\"><img src=\"' + this.a_tpl.imgpath + 'prev_mon.gif\" /></td><th>'+ this.a_tpl.months[d_date.getMonth()] + ' ' + d_date.getFullYear()+ '</th><td' + this.f_relDate(d_date, 1) + ' title=\"".__("Next Month","myStat")."\"><img src=\"' + this.a_tpl.imgpath + 'next_mon.gif\" /></td>'+ (this.a_tpl.yearscroll ? '<td' + this.f_relDate(d_date, 1, 'y') + ' title=\"".__("Next Year","myStat")."\"><img src=\"' + this.a_tpl.imgpath + 'next_year.gif\" /></td></td>' : '')+ '</tr></tbody></table><table><tbody><tr class=\"wd\">';for (var i = 0; i < 7; i++)s_html += '<th>' + this.a_tpl.weekdays[(this.a_tpl.weekstart + i) % 7] + '</th>';s_html += '</tr>' ;var d_current = new Date(d_firstday);while (d_current.getMonth() == d_date.getMonth() ||d_current.getMonth() == d_firstday.getMonth()) {s_html +='<tr>';for (var n_wday = 0; n_wday < 7; n_wday++) {a_class = [];if (d_current.getMonth() != d_date.getMonth())a_class[a_class.length] = 'othermonth';if (d_current.getDay() == 0 || d_current.getDay() == 6)a_class[a_class.length] = 'weekend';if (d_current.valueOf() == d_today.valueOf())a_class[a_class.length] = 'today';if (d_current.valueOf() == d_selected.valueOf())a_class[a_class.length] = 'selected';s_html += '<td onclick=\"A_TCALS[\\'' + this.s_id + '\\'].f_hide(' + d_current.valueOf() + ')\"' + (a_class.length ? ' class=\"' + a_class.join(' ') + '\">' : '>') + d_current.getDate() + '</td>';d_current.setDate(d_current.getDate() + 1);};s_html +='</tr>';};s_html +='</tbody></table>';this.e_div.innerHTML = s_html;var n_width  = this.e_div.offsetWidth;var n_height = this.e_div.offsetHeight;var n_top  = f_getPosition (this.e_icon, 'Top') + this.e_icon.offsetHeight;var n_left = f_getPosition (this.e_icon, 'Left') - n_width + this.e_icon.offsetWidth;if (n_left < 0) n_left = 0;this.e_div.style.left = n_left + 'px';this.e_div.style.top  = n_top + 'px';this.e_shade.style.width = (n_width + 8) + 'px';this.e_shade.style.left = (n_left - 1) + 'px';this.e_shade.style.top = (n_top - 1) + 'px';this.e_shade.innerHTML = b_ieFix? '<table><tbody><tr><td rowspan=\"2\" colspan=\"2\" width=\"6\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td><td width=\"7\" height=\"7\" style=\"filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\\'' + this.a_tpl.imgpath + 'shade_tr.png\\', sizingMethod=\\'scale\\');\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td></tr><tr><td height=\"' + (n_height - 7) + '\" style=\"filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\\'' + this.a_tpl.imgpath + 'shade_mr.png\\', sizingMethod=\\'scale\\');\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td></tr><tr><td width=\"7\" style=\"filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\\'' + this.a_tpl.imgpath + 'shade_bl.png\\', sizingMethod=\\'scale\\');\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td><td style=\"filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\\'' + this.a_tpl.imgpath + 'shade_bm.png\\', sizingMethod=\\'scale\\');\" height=\"7\" align=\"left\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td><td style=\"filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\\'' + this.a_tpl.imgpath + 'shade_br.png\\', sizingMethod=\\'scale\\');\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td></tr><tbody></table>': '<table><tbody><tr><td rowspan=\"2\" width=\"6\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td><td rowspan=\"2\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td><td width=\"7\" height=\"7\"><img src=\"' + this.a_tpl.imgpath + 'shade_tr.png\"></td></tr><tr><td style=\"background-image:url(' + this.a_tpl.imgpath + 'shade_mr.png)\" height=\"' + (n_height - 7) + '\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td></tr><tr><td><img src=\"' + this.a_tpl.imgpath + 'shade_bl.png\"></td><td style=\"background-image:url(' + this.a_tpl.imgpath + 'shade_bm.png)\" height=\"7\" align=\"left\"><img src=\"' + this.a_tpl.imgpath + 'pixel.gif\"></td><td><img src=\"' + this.a_tpl.imgpath + 'shade_br.png\"></td></tr><tbody></table>';if (this.e_iframe) {this.e_iframe.style.left = n_left + 'px';this.e_iframe.style.top  = n_top + 'px';this.e_iframe.style.width = (n_width + 6) + 'px';this.e_iframe.style.height = (n_height + 6) +'px';};return true;};function f_getPosition (e_elemRef, s_coord) {var n_pos = 0, n_offset,e_elem = e_elemRef;while (e_elem) {n_offset = e_elem[\"offset\" + s_coord];n_pos += n_offset;e_elem = e_elem.offsetParent;};if (b_ieMac)n_pos += parseInt(document.body[s_coord.toLowerCase() + 'Margin']);else if (b_safari)n_pos -= n_offset;e_elem = e_elemRef;while (e_elem != document.body) {n_offset = e_elem[\"scroll\" + s_coord];if (n_offset && e_elem.style.overflow == 'scroll')n_pos -= n_offset;e_elem = e_elem.parentNode;};return n_pos;};function f_tcalRelDate (d_date, d_diff, s_units) {var s_units = (s_units == 'y' ? 'FullYear' : 'Month');var d_result = new Date(d_date);d_result['set' + s_units](d_date['get' + s_units]() + d_diff);if (d_result.getDate() != d_date.getDate())d_result.setDate(0);return ' onclick=\"A_TCALS[\\'' + this.s_id + '\\'].f_update(' + d_result.valueOf() + ')\"';};function f_tcalHideAll () {for (var i = 0; i < window.A_TCALSIDX.length; i++)window.A_TCALSIDX[i].f_hide();};f_getElement = document.all ?function (s_id) { return document.all[s_id] } :function (s_id) { return document.getElementById(s_id) };if (document.addEventListener)window.addEventListener('scroll', f_tcalHideAll, false);if (window.attachEvent)window.attachEvent('onscroll', f_tcalHideAll);var s_userAgent = navigator.userAgent.toLowerCase(),re_webkit = /WebKit\\/(\\d+)/i;var b_mac = s_userAgent.indexOf('mac') != -1,b_ie5 = s_userAgent.indexOf('msie 5') != -1,b_ie6 = s_userAgent.indexOf('msie 6') != -1 && s_userAgent.indexOf('opera') == -1;var b_ieFix = b_ie5 || b_ie6,b_ieMac  = b_mac && b_ie5,b_safari = b_mac && re_webkit.exec(s_userAgent) && Number(RegExp.$1) < 500;";
        echo "</script>";
    }

    function avg($arr){
        $count=count($arr);$sum=0;
        for($i=0;$i<count($arr);$i++){
            $sum+=$arr[$i][0];
        };
        $avg=$count>0?$sum/$count:0;
        return $avg;
    }

    function google_ext_encode($dec) {
    	if($dec === false) return '__';

    	$e = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
    		'abcdefghijklmnopqrstuvwxyz0123456789-.';
    	$m = strlen($e); // == 63
    	$res = '';

    	do {
    		$r = $dec % $m;
    		$dec = (int)($dec / $m);
    		$res = $e[$r].$res;
    	} while($dec > 0);

    	$res = sprintf("%'A2s", $res);
    	return $res;
    }

    function chart_html($type, $width, $height, $data, $labels = false, $colors = array("00DDAA"), $alt = '') {
    	$edata = implode('', (array_map(Array("myStat_common","google_ext_encode"), $data)));
    	$url = 'http://chart.apis.google.com/chart?cht='.$type.
    		'&chd=e:'.$edata.'&chs='.$width.'x'.$height.'&chf=bg,s,F2F2F2';
    	if(is_array($colors) && count($colors))
    		$url .= '&chco='.implode(',', $colors);
    	if(is_array($labels) && count($labels))
    		$url .= '&chl='.implode('|', (array_map("urlencode", $labels)));
    	return '<img src="'.$url.'" width="'.$width.'" height="'.$height.
    		'" alt="'.$alt.'" />';
    }

    function setDebug($string)
    {
      if('777'==substr(sprintf('%o', fileperms(dirname(__FILE__).'/../data/')), -3)){
        $file = dirname(__FILE__).'/../data/debug.log';
        $fp = fopen($file, 'a+');
        fwrite($fp,time().' '.$string."\n");
        fclose($fp);
      };
    }

    function dbConnect()
    {
      $this->prefix = $GLOBALS['wpdb']->prefix;
      if(is_null($this->connect)){
        $this->connect = $GLOBALS['wpdb'];
      }
      return $this->connect;
    }

    function getSQL($sql,$return = true)
    {
      $conn = $this->dbConnect();
      $sql = str_replace("%%PREFIX%%",$this->prefix,$sql);
      if($return){
        $var=$conn->get_results($sql, ARRAY_N);
        return $var;
      }else{
        $conn->query($sql);
        return true;
      };
    }

    function getSQLONE($sql){
      $var = $this->getSQL($sql);
      if(count($var)>=1){
        if(count($var[0])>1){
          return $var[0];
        }else{
          return $var[0][0];
        };
      }else{
        return false;
      };
    }

    function getPrefix()
    {
      
      return $this->prefix!=''?$this->prefix:$GLOBALS['wpdb']->prefix;
    }

    function getParam($name,$default='')
    {
      return get_option($name);
    }

    function setParam($name,$value)
    {
      if(self::getParam($name)!=''){
        update_option($name,$value);
      }else{
        add_option($name,$value);
      };
    }

    function delParam($name)
    {
      delete_option($name);
    }
};
?>