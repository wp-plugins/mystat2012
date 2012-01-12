<?php
    echo "<style>body{font-size:11px;font-family:Tahoma;background-color:#e1f8d5}</style>";
    echo "<center><h3>TEST YOUR SERVER v2.6</h3></center>";
    echo "<br/><center><table cellspacing='0' cellpadding='0' style='border:solid #DDDDDD 1px;-moz-border-radius:6px;margin:3px;padding:5px;' width='60%' align='center'>";
    echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>PHP Version</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>".(function_exists("phpversion")?phpversion():"NO")."</td></tr>";
    echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>APACHE Version</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>".(function_exists("apache_get_version")?apache_get_version():"NO")."</td></tr>";
    include_once("../../../wp-config.php");
    echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>WORDPRESS Version</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>".$GLOBALS['wp_version']."</td></tr>";
    $db=mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>MYSQL Version</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>".(function_exists("mysql_get_server_info")?mysql_get_server_info():"NO")."</td></tr>";
    echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>GD Version</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>";
    if(function_exists(gd_info)){$a=gd_info();echo $a["GD Version"];}else echo "NO";
    echo "</td></tr>";
    echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>GEO DAT file</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>".(file_exists("data/GeoIP.dat")?"YES":"NO")."</td></tr>";
    echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>GEO CITY DAT file</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>".(file_exists("data/GeoIPCity.dat")?"YES":"NO")."</td></tr>";
    $var=mysql_db_query(DB_NAME,"SELECT count(*) FROM ".$GLOBALS['wpdb']->prefix."myStat_data;",$db);
    $row = mysql_fetch_array($var, MYSQL_NUM);
    $var=mysql_db_query(DB_NAME,"SHOW FIELDS FROM ".$GLOBALS['wpdb']->prefix."myStat_data;",$db);
    echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top' nowrap><b>TABLE ".$GLOBALS['wpdb']->prefix."myStat_data</b> (rows: ".$row[0].")</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:left;'>";
    while ($row = mysql_fetch_array($var, MYSQL_NUM)){
        printf ("<b>FIELD</b>: <i>%s</i>  <b>TYPE</b>: <i>%s</i><br/>", $row[0], $row[1]);
    };
    mysql_free_result($var);
    $var=mysql_db_query(DB_NAME,"SHOW INDEX FROM ".$GLOBALS['wpdb']->prefix."myStat_data;",$db);
    echo "<br/>";
    while ($row = mysql_fetch_array($var, MYSQL_NUM)){
        printf ("<b>INDEX</b>: <i>%s</i>  <b>COLUMN</b>: <i>%s</i><br/>", $row[2], $row[4]);
    };
    mysql_free_result($var);
    echo "</td></tr>";
    $var=mysql_db_query(DB_NAME,"SELECT count(*) FROM ".$GLOBALS['wpdb']->prefix."myStat_dbsize;",$db);
    $row = mysql_fetch_array($var, MYSQL_NUM);
    $var=mysql_db_query(DB_NAME,"SHOW FIELDS FROM ".$GLOBALS['wpdb']->prefix."myStat_dbsize;",$db);
    echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top' nowrap><b>TABLE ".$GLOBALS['wpdb']->prefix."myStat_dbsize</b> (rows: ".$row[0].")</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:left;'>";
    while ($row = mysql_fetch_array($var, MYSQL_NUM)){
        printf ("<b>FIELD</b>: <i>%s</i>  <b>TYPE</b>: <i>%s</i><br/>", $row[0], $row[1]);
    };
    mysql_free_result($var);
    $var=mysql_db_query(DB_NAME,"SHOW INDEX FROM ".$GLOBALS['wpdb']->prefix."myStat_dbsize;",$db);
    echo "<br/>";
    while ($row = mysql_fetch_array($var, MYSQL_NUM)){
        printf ("<b>INDEX</b>: <i>%s</i>  <b>COLUMN</b>: <i>%s</i><br/>", $row[2], $row[4]);
    };
    mysql_free_result($var);
    echo "</td></tr>";
    $var=mysql_db_query(DB_NAME,"SELECT count(*) FROM ".$GLOBALS['wpdb']->prefix."myStat_main;",$db);
    $row = mysql_fetch_array($var, MYSQL_NUM);
    $var=mysql_db_query(DB_NAME,"SHOW FIELDS FROM ".$GLOBALS['wpdb']->prefix."myStat_main;",$db);
    echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top' nowrap><b>TABLE ".$GLOBALS['wpdb']->prefix."myStat_main</b> (rows: ".$row[0].")</td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:left;'>";
    while ($row = mysql_fetch_array($var, MYSQL_NUM)){
        printf ("<b>FIELD</b>: <i>%s</i>  <b>TYPE</b>: <i>%s</i><br/>", $row[0], $row[1]);
    };
    mysql_free_result($var);
    $var=mysql_db_query(DB_NAME,"SHOW INDEX FROM ".$GLOBALS['wpdb']->prefix."myStat_main;",$db);
    echo "<br/>";
    while ($row = mysql_fetch_array($var, MYSQL_NUM)){
        printf ("<b>INDEX</b>: <i>%s</i>  <b>COLUMN</b>: <i>%s</i><br/>", $row[2], $row[4]);
    };
    mysql_free_result($var);
    echo "</td></tr>";
    echo "<tr style='background-color:#E6E6E6'><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>ZLIB</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>".(function_exists("gzfile")?"YES":"NO")."</td></tr>";
    mysql_close($db);
    echo "<tr><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;' valign='top'><b>CHMOD data DIR</b></td><td style='padding:5px;font-size:11px;height:25px;border:solid #DDDDDD 1px;text-align:center;'>".(substr(decoct( fileperms(dirname(__FILE__).'/data/') ), 2)=='777'?"YES":"NO")."</td></tr>";
    echo "</table></center>";


?>