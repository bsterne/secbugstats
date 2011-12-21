<?php
include_once("./config.php");

function logToFile($filename, $msg){
  $fp = fopen($filename, "a");
  fwrite($fp, "[".date("Y-m-d G:i:s")."] - ".$msg."\n");
  fclose($fp);
}

function outputDetails(){
  $myCategory = htmlentities(mysql_real_escape_string($_REQUEST["category"]));
  $myDate = htmlentities(mysql_real_escape_string($_REQUEST["date"]));

  // get total number of bugs for this group
  //$sql = sprintf("select sum(d.count) as total from Details d, Stats s where d.sid=s.sid and s.category='%s' and d.date LIKE '%s%%';", $myCategory, $myDate);
  $sql = sprintf("select count from Stats where category='%s' and date LIKE '%s%%';", $myCategory, $myDate);
  //print $sql;
  $result = mysql_query($sql);
  $row = mysql_fetch_assoc($result);
  $myTotal = (int)$row["count"];

  $sql = sprintf("select d.did, d.sid, d.date, d.product, d.component, d.count, d.bug_list, d.avg_age_days, d.med_age_days from Details d, Stats s where d.sid=s.sid and s.category='%s' and d.date LIKE '%s%%' order by d.date, d.count desc, d.product, d.component;", $myCategory, $myDate);
  //print $sql;
  $result = mysql_query($sql);
  if(!mysql_num_rows($result)){
    printf("<details category=\"%s\" total=\"%s\"></details>\n", $myCategory, $myTotal);
    return;
  }

  printf("<details category=\"%s\" total=\"%s\">\n", $myCategory, $myTotal);
  
  while($row = mysql_fetch_assoc($result)){
    print "<detail>\n";
    printf("<did>%s</did><sid>%s</sid><date>%s</date><product>%s</product><component>%s</component><count>%s</count><bug_list>%s</bug_list><avg_age_days>%s</avg_age_days><med_age_days>%s</med_age_days><total_age_days>%s</total_age_days>\n", $row["did"], $row["sid"], $row["date"], htmlentities($row["product"]), htmlentities($row["component"]), $row["count"], $row["bug_list"], $row["avg_age_days"], $row["med_age_days"], $row["total_age_days"]);
    print "</detail>\n";
  }

  print "</details>\n";
}

//logToFile("ws-preview.php.log", print_r($_REQUEST, True));

header("Content-Type: text/xml");
outputDetails();
?>
